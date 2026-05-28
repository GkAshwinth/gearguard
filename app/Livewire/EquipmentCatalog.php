<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipment;

class EquipmentCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    // Reset pagination when the user types or changes a category
    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }

    public function render()
    {
        $query = Equipment::where('status', 'available');

        // Apply search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        // Apply category filter
        if ($this->category) {
            $query->where('category', $this->category);
        }

        return view('livewire.equipment-catalog', [
            'equipments' => $query->paginate(9),
            'categories' => Equipment::select('category')->distinct()->pluck('category'),
        ]);
    }
}
