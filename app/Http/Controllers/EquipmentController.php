<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Http\Requests\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /**
     * Public catalog — visible to all visitors.
     */
    public function index()
    {
        return view('client.browse');
    }

    /**
     * Show single equipment item.
     */
    public function show(Equipment $equipment)
    {
        // Load busy dates for the date picker (calendar blocking)
        $busyDates = $equipment->bookings()
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where('end_date', '>=', now())
            ->get(['start_date', 'end_date']);

        return view('client.product', compact('equipment', 'busyDates'));
    }

    // ── Owner (Admin) Methods ──────────────────────────────────────────────

    /**
     * Owner: list all inventory items.
     */
    public function adminIndex()
    {
        $equipment = Equipment::withCount('bookings')->latest()->paginate(20);
        return view('owner.inventory', compact('equipment'));
    }

    /**
     * Owner: show create form.
     */
    public function create()
    {
        return view('owner.create_product');
    }

    /**
     * Owner: store new equipment.
     * Uses EquipmentRequest for validated, sanitised input.
     */
    public function store(EquipmentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($data);

        return redirect()->route('owner.inventory')
            ->with('success', 'Equipment added successfully.');
    }

    /**
     * Owner: show edit form.
     */
    public function edit(Equipment $equipment)
    {
        return view('owner.edit_product', compact('equipment'));
    }

    /**
     * Owner: update equipment record.
     */
    public function update(EquipmentRequest $request, Equipment $equipment)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if stored locally
            if ($equipment->image_path && !str_starts_with($equipment->image_path, 'http')) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            $data['image_path'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($data);

        return redirect()->route('owner.inventory')
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Owner: delete equipment.
     */
    public function destroy(Equipment $equipment)
    {
        if ($equipment->image_path && !str_starts_with($equipment->image_path, 'http')) {
            Storage::disk('public')->delete($equipment->image_path);
        }

        $equipment->delete();

        return redirect()->route('owner.inventory')
            ->with('success', 'Equipment removed from inventory.');
    }
}
