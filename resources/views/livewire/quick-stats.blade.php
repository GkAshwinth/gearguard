<?php
use Livewire\Volt\Component;
use App\Models\Equipment;

new class extends Component {
    public int $totalAvailable;

    public function mount() {
        $this->totalAvailable = Equipment::where('status', 'available')->count();
    }
}; ?>

<div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-center">
    <span class="text-sm text-indigo-600 font-bold uppercase tracking-wide">Available Right Now</span>
    <div class="text-2xl font-black text-indigo-900">{{ $totalAvailable }} Items</div>
</div>
