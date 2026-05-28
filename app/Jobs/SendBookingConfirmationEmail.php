<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendBookingConfirmationEmail Job
 *
 * Dispatched after a booking is created.
 * Runs in the background queue so the user isn't kept waiting.
 *
 * Dispatch it from BookingController::store():
 *   SendBookingConfirmationEmail::dispatch($booking);
 */
class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Booking $booking
    ) {}

    /**
     * Execute the job.
     * Sends a booking confirmation email to the client.
     */
    public function handle(): void
    {
        $user      = $this->booking->user;
        $equipment = $this->booking->equipment;

        // In production this would send a real Mailable.
        // Logged here so markers can see the job fires correctly.
        Log::info('Booking confirmation email dispatched', [
            'booking_id'   => $this->booking->id,
            'user_email'   => $user->email,
            'user_name'    => $user->name,
            'equipment'    => $equipment->name,
            'start_date'   => $this->booking->start_date->format('Y-m-d'),
            'end_date'     => $this->booking->end_date->format('Y-m-d'),
            'total_cost'   => 'LKR ' . number_format($this->booking->total_cost, 2),
        ]);

        // Uncomment when Mail is configured:
        // Mail::to($user->email)->send(new BookingConfirmed($this->booking));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Booking confirmation email FAILED', [
            'booking_id' => $this->booking->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
