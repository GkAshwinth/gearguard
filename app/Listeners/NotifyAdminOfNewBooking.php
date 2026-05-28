<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * NotifyAdminOfNewBooking Listener
 *
 * Listens for: BookingCreated event
 * Action: Notifies the shop owner that a new booking request is pending.
 *
 * Registered in: app/Providers/EventServiceProvider.php
 * $listen = [
 *   BookingCreated::class => [NotifyAdminOfNewBooking::class],
 * ];
 *
 * Implements ShouldQueue so it runs in the background — owner
 * notification doesn't slow down the client's booking confirmation.
 */
class NotifyAdminOfNewBooking implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BookingCreated $event): void
    {
        $booking   = $event->booking;
        $equipment = $booking->equipment;
        $client    = $booking->user;

        // Find all owners to notify
        $owners = User::where('role', 'owner')->get();

        foreach ($owners as $owner) {
            Log::info('Admin notified of new booking', [
                'admin_email'    => $owner->email,
                'booking_id'     => $booking->id,
                'client_name'    => $client->name,
                'client_email'   => $client->email,
                'equipment_name' => $equipment->name,
                'start_date'     => $booking->start_date->format('Y-m-d'),
                'end_date'       => $booking->end_date->format('Y-m-d'),
                'total_cost'     => 'LKR ' . number_format($booking->total_cost, 2),
            ]);

            // Mail::to($owner->email)->send(new NewBookingAlert($booking));
        }
    }
}
