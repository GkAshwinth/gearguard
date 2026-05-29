<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Advanced Feature: Custom Artisan Command to clean up old bookings
Artisan::command('bookings:clean', function () {
    $count = Booking::where('status', 'cancelled')
        ->where('created_at', '<', now()->subDays(30))
        ->delete();
    
    $this->info("Cleaned up {$count} old cancelled bookings from the database.");
})->purpose('Delete cancelled bookings older than 30 days');

// Advanced Feature: Task Scheduling (runs automatically via cron)
Schedule::command('bookings:clean')->dailyAt('02:00');
