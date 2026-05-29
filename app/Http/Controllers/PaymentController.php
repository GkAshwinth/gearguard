<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Events\BookingCreated;
use App\Jobs\SendBookingConfirmationEmail;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $bookingId = $request->query('booking');

        if (!$sessionId || !$bookingId) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment session.');
        }

        $stripeSecret = env('STRIPE_SECRET', 'sk_test_mock');

        // MOCK BYPASS: Accept mock session IDs for testing/demo purposes without Stripe
        if ($stripeSecret === 'sk_test_mock' && str_starts_with($sessionId, 'mock_session_')) {
            $booking = Booking::findOrFail($bookingId);
            
            $booking->update([
                'payment_status' => 'paid',
                'transaction_id' => $sessionId,
            ]);

            SendBookingConfirmationEmail::dispatch($booking);
            BookingCreated::dispatch($booking);

            return redirect()->route('dashboard')->with('success', 'Payment successful (Mock Mode)! Your booking is now awaiting admin approval.');
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $booking = Booking::findOrFail($bookingId);
                
                $booking->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $session->payment_intent,
                ]);

                // Dispatch confirmation emails
                SendBookingConfirmationEmail::dispatch($booking);
                BookingCreated::dispatch($booking);

                return redirect()->route('dashboard')->with('success', 'Payment successful! Your booking is now awaiting admin approval.');
            }
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Could not verify payment: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('error', 'Payment was not successful.');
    }

    public function cancel(Request $request)
    {
        $bookingId = $request->query('booking');
        
        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking && $booking->payment_status === 'unpaid') {
                $booking->delete();
            }
        }

        return redirect()->route('dashboard')->with('error', 'Payment cancelled.');
    }
}
