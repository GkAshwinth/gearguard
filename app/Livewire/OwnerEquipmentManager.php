<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Equipment;
use App\Models\Scopes\ActiveEquipmentScope;

class OwnerEquipmentManager extends Component
{
    use WithPagination, WithFileUploads;

    // Modal state
    public $isModalOpen = false;
    public $isEditing = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $category = '';
    public $daily_rate = '';
    public $description = '';
    public $status = 'available';
    public $image; // For the uploaded file
    public $existing_image_url;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'description' => 'required|string',
            'status' => 'required|in:available,rented,maintenance',
        ];

        if (!$this->isEditing || $this->image) {
            $rules['image'] = 'required|image|mimes:jpeg,png,webp|max:2048';
        }

        return $rules;
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditing = false;
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetFields();
        $this->isEditing = true;
        $this->editingId = $id;

        $equipment = Equipment::withoutGlobalScope(ActiveEquipmentScope::class)->findOrFail($id);
        
        $this->name = $equipment->name;
        $this->category = $equipment->category;
        $this->daily_rate = $equipment->getRawOriginal('daily_rate'); // Use raw float
        $this->description = $equipment->description;
        $this->status = $equipment->status;
        $this->existing_image_url = $equipment->image_url;

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
        $this->dispatch('close-modal');
    }

    public function resetFields()
    {
        $this->name = '';
        $this->category = '';
        $this->daily_rate = '';
        $this->description = '';
        $this->status = 'available';
        $this->image = null;
        $this->existing_image_url = null;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        if (!auth()->user()->isOwner()) abort(403);
        
        $this->validate();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'daily_rate' => $this->daily_rate,
            'description' => $this->description,
            'status' => $this->status,
        ];

        if ($this->image) {
            // Generate unique name and store in public disk under 'equipment' folder
            $path = $this->image->store('equipment', 'public');
            $data['image_path'] = $path;
        }

        if ($this->isEditing) {
            $equipment = Equipment::withoutGlobalScope(ActiveEquipmentScope::class)->findOrFail($this->editingId);
            
            // Delete old image if a new one is uploaded and old was local
            if ($this->image && $equipment->image_path && !str_starts_with($equipment->image_path, 'http')) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            
            $equipment->update($data);
        } else {
            Equipment::create($data);
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        if (!auth()->user()->isOwner()) abort(403);
        
        $equipment = Equipment::withoutGlobalScope(ActiveEquipmentScope::class)->findOrFail($id);
        
        // Let SoftDeletes handle it, don't physically delete the image or row yet
        $equipment->delete();
    }

    public function render()
    {
        $equipment = Equipment::withoutGlobalScope(ActiveEquipmentScope::class)
            ->withCount('bookings')
            ->latest()
            ->paginate(15);
            
        return view('livewire.owner-equipment-manager', [
            'equipmentList' => $equipment
        ]);
    }
}
