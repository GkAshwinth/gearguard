<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class CleanupBookings extends Command
{
    protected $signature = 'gearguard:cleanup';
    protected $description = 'Cancel pending bookings older than 48 hours';

    public function handle()
    {
        $count = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(2))
            ->update(['status' => 'cancelled']);

        $this->info("Cleaned up {$count} expired pending bookings!");
    }
}
