<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\StripeAccount;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $this->verifySignature($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Invalid Stripe webhook signature', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        Log::info('Stripe webhook received', [
            'type' => $event['type'],
            'id' => $event['id'],
        ]);

        try {
            match($event['type']) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($event),
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event),
                'account.updated' => $this->handleAccountUpdated($event),
                'charge.refunded' => $this->handleChargeRefunded($event),
                default => null,
            };

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing Stripe webhook', [
                'type' => $event['type'],
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    protected function verifySignature(string $payload, ?string $signature): void
    {
        if (!$signature) {
            throw new \Exception('No signature provided');
        }

        $secret = config('stripe.webhook_secret');
        if (!$secret) {
            throw new \Exception('No webhook secret configured');
        }

        $signatureParts = explode(',', $signature);
        $timestamp = null;
        $signatures = [];

        foreach ($signatureParts as $part) {
            $part = trim($part);
            [$key, $value] = explode('=', $part, 2);
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || empty($signatures)) {
            throw new \Exception('Invalid signature header');
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSignature, $sig)) {
                return;
            }
        }

        throw new \Exception('Invalid signature');
    }

    protected function handleCheckoutCompleted(array $event): void
    {
        $session = $event['data']['object'];
        $bookingId = $session['metadata']['booking_id'] ?? null;

        if (!$bookingId) {
            Log::warning('Checkout session has no booking_id', ['session_id' => $session['id']]);
            return;
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            Log::warning('Booking not found', ['booking_id' => $bookingId]);
            return;
        }

        $paymentIntentId = $session['payment_intent'] ?? null;
        
        $this->bookingService->confirmBooking($booking, $paymentIntentId);

        Log::info('Booking confirmed via checkout', [
            'booking_id' => $booking->id,
            'session_id' => $session['id'],
        ]);
    }

    protected function handlePaymentSucceeded(array $event): void
    {
        $paymentIntent = $event['data']['object'];
        $bookingId = $paymentIntent['metadata']['booking_id'] ?? null;

        if (!$bookingId) {
            return;
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return;
        }

        $this->bookingService->confirmBooking($booking, $paymentIntent['id']);

        Log::info('Booking payment succeeded', [
            'booking_id' => $booking->id,
            'payment_intent_id' => $paymentIntent['id'],
        ]);
    }

    protected function handlePaymentFailed(array $event): void
    {
        $paymentIntent = $event['data']['object'];
        $bookingId = $paymentIntent['metadata']['booking_id'] ?? null;

        if (!$bookingId) {
            return;
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return;
        }

        $booking->update([
            'payment_status' => 'failed',
            'stripe_payment_intent_id' => $paymentIntent['id'],
        ]);

        Log::info('Booking payment failed', [
            'booking_id' => $booking->id,
            'payment_intent_id' => $paymentIntent['id'],
        ]);
    }

    protected function handleAccountUpdated(array $event): void
    {
        $account = $event['data']['object'];
        $accountId = $account['id'];

        $stripeAccount = StripeAccount::where('stripe_account_id', $accountId)->first();
        if (!$stripeAccount) {
            return;
        }

        $stripeAccount->update([
            'charges_enabled' => $account['charges_enabled'] ?? false,
            'payouts_enabled' => $account['payouts_enabled'] ?? false,
            'details_submitted' => $account['details_submitted'] ?? false,
            'onboarding_completed' => ($account['details_submitted'] ?? false) 
                && ($account['charges_enabled'] ?? false),
        ]);

        Log::info('Stripe account updated', [
            'account_id' => $accountId,
            'coach_id' => $stripeAccount->coach_id,
        ]);
    }

    protected function handleChargeRefunded(array $event): void
    {
        $charge = $event['data']['object'];
        $paymentIntentId = $charge['payment_intent'] ?? null;

        if (!$paymentIntentId) {
            return;
        }

        $booking = Booking::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if (!$booking) {
            return;
        }

        $booking->update([
            'payment_status' => 'refunded',
            'stripe_charge_id' => $charge['id'],
        ]);

        Log::info('Booking refunded', [
            'booking_id' => $booking->id,
            'charge_id' => $charge['id'],
        ]);
    }
}
