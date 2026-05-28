<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BookingCreated Event
 *
 * Fired after a booking is successfully persisted.
 * Listeners: NotifyAdminOfNewBooking, SendBookingConfirmationEmail (via listener)
 */
class BookingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Booking $booking
    ) {}
}
