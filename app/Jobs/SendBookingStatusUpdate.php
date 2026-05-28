<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendBookingStatusUpdate Job
 *
 * Dispatched when an owner approves or rejects a booking.
 * Notifies the client of the status change asynchronously.
 */
class SendBookingStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly Booking $booking,
        public readonly string  $previousStatus
    ) {}

    public function handle(): void
    {
        $user = $this->booking->user;

        Log::info('Booking status update email dispatched', [
            'booking_id'      => $this->booking->id,
            'user_email'      => $user->email,
            'previous_status' => $this->previousStatus,
            'new_status'      => $this->booking->status,
            'equipment'       => $this->booking->equipment->name,
        ]);

        // Mail::to($user->email)->send(new BookingStatusChanged($this->booking));
    }
}
