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
        // 1. Cache the categories list forever (invalidated automatically in EquipmentObserver)
        $categories = \Illuminate\Support\Facades\Cache::rememberForever('equipment_categories', function () {
            return Equipment::select('category')->distinct()->pluck('category');
        });

        // 2. Cache the paginated results based on the current search, category, and page
        $cacheKey = 'equipment_catalog_' . md5($this->search . '_' . $this->category . '_' . $this->getPage());
        
        $equipments = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () {
            $query = Equipment::all()->toQuery(); // Applies global scope safely
            
            if ($this->search) {
                $query->where('name', 'like', '%' . $this->search . '%');
            }
            if ($this->category) {
                $query->where('category', $this->category);
            }
            return $query->paginate(9);
        });

        return view('livewire.equipment-catalog', compact('equipments', 'categories'));
    }
}
