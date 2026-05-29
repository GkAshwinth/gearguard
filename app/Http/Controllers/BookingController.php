<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Jobs\SendBookingConfirmationEmail;
use App\Jobs\SendBookingStatusUpdate;
use App\Models\Booking;
use App\Models\Equipment;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Client: show booking checkout page.
     */
    public function checkout(Request $request, Equipment $equipment)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$startDate || !$endDate) {
            return redirect()->route('equipment.show', $equipment)
                ->with('error', 'Please select rental dates.');
        }

        if (!$equipment->isAvailableFor($startDate, $endDate)) {
            return redirect()->route('equipment.show', $equipment)
                ->with('error', 'Those dates are no longer available.');
        }

        $days  = (new \DateTime($startDate))->diff(new \DateTime($endDate))->days + 1;
        $total = $days * $equipment->daily_rate;

        return view('client.checkout', compact('equipment', 'startDate', 'endDate', 'days', 'total'));
    }

    /**
     * Store the new booking securely.
     */
    public function store(Request $request)
    {
        // 1. Strict Input Validation
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $booking = null;

        // 2. Database Transaction to prevent Race Conditions (Double Bookings)
        DB::transaction(function () use ($validated, &$booking) {
            
            // Lock the table row to double-check nobody booked it in the last millisecond
            $conflict = Booking::where('equipment_id', $validated['equipment_id'])
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                          ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']]);
                })->lockForUpdate()->exists();

            if ($conflict) {
                abort(409, 'Sorry, this equipment was just booked by someone else.');
            }

            // Calculate total cost securely on the server side to satisfy DB constraint
            $equipment = Equipment::withoutGlobalScopes()->findOrFail($validated['equipment_id']);
            $days = \Carbon\Carbon::parse($validated['start_date'])->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
            $totalCost = $days * (float) $equipment->getRawOriginal('daily_rate');

            // 3. Save the booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'equipment_id' => $validated['equipment_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_cost' => $totalCost,
                'status' => 'pending', // Requires admin approval
            ]);
        });

        // Dispatch background job and event
        if ($booking) {
            SendBookingConfirmationEmail::dispatch($booking);
            BookingCreated::dispatch($booking);
        }

        // Redirect back to dashboard with a success message
        return redirect()->route('dashboard')->with('success', 'Booking submitted successfully! Waiting for admin approval.');
    }

    /**
     * Client: view own bookings dashboard.
     */
    public function clientDashboard()
    {
        $bookings = auth()->user()->bookings()
            ->with('equipment')
            ->latest()
            ->paginate(10);

        return view('client.dashboard', compact('bookings'));
    }

    /**
     * Owner: view all bookings.
     */
    public function ownerDashboard()
    {
        $pending   = Booking::with(['user', 'equipment'])->pending()->latest()->get();
        $active    = Booking::with(['user', 'equipment'])->active()->latest()->get();
        $overdue   = Booking::with(['user', 'equipment'])->overdue()->latest()->get();
        $all       = Booking::with(['user', 'equipment'])->latest()->paginate(20);

        $totalRevenue = Booking::where('status', 'approved')->sum('total_cost');
        $activeCount  = Booking::active()->count();
        $pendingCount = Booking::pending()->count();
        $itemCount    = Equipment::withoutGlobalScopes()->count();

        return view('owner.dashboard', compact(
            'pending', 'active', 'overdue', 'all',
            'totalRevenue', 'activeCount', 'pendingCount', 'itemCount'
        ));
    }

    /**
     * Owner: approve or reject a booking.
     * SECURITY: Status validated against allowlist — no raw user input reaches DB.
     * JOB: Dispatches SendBookingStatusUpdate to notify client.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected,completed'],
        ]);

        $previousStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // Dispatch job to notify client of status change
        SendBookingStatusUpdate::dispatch($booking, $previousStatus);

        return redirect()->route('owner.dashboard')
            ->with('success', "Booking #{$booking->id} marked as {$request->status}.");
    }

    /**
     * Client: cancel own pending booking.
     * SECURITY: Ownership check prevents IDOR.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'You cannot cancel this booking.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('client.dashboard')
                ->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('client.dashboard')
            ->with('success', 'Booking cancelled successfully.');
    }
}
