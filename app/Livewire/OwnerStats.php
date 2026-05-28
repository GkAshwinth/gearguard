<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;

class OwnerStats extends Component
{
    public function render()
    {
        return view('livewire.owner-stats', [
            'pendingCount' => Booking::where('status', 'pending')->count(),
            'activeRentals' => Booking::where('status', 'approved')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->count(),
            // Securely calculate total revenue based on DB column total_cost
            'totalRevenue' => Booking::where('status', 'completed')->sum('total_cost') ?? 0, 
        ]);
    }
}
