<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;

class OwnerDashboard extends Component
{
    public function render()
    {
        return view('livewire.owner-dashboard', [
            'pendingCount' => Booking::where('status', 'pending')->count(),
            'activeRentals' => Booking::where('status', 'approved')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->count(),
            // Assumes you have a total_cost or total_price column
            'totalRevenue' => Booking::where('status', 'completed')->sum('total_cost') ?? 0, 
        ]);
    }
}
