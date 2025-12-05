<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FungiesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FungiesWebhookController extends Controller
{
    protected FungiesService $fungiesService;

    public function __construct(FungiesService $fungiesService)
    {
        $this->fungiesService = $fungiesService;
    }

    /**
     * Handle incoming Fungies.io webhooks
     */
    public function handle(Request $request)
    {
        // Get the raw payload and signature
        $payload = $request->getContent();
        $signature = $request->header('X-Fungies-Signature');

        // Verify webhook signature
        if (!$this->fungiesService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Fungies webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Parse the webhook data
        $data = json_decode($payload, true);
        $eventType = $data['event'] ?? null;

        if (!$eventType) {
            Log::error('Fungies webhook missing event type', ['data' => $data]);
            return response()->json(['error' => 'Missing event type'], 400);
        }

        Log::info('Fungies webhook received', [
            'event' => $eventType,
            'data' => $data,
        ]);

        // Route to appropriate handler
        try {
            match ($eventType) {
                'payment_success' => $this->handlePaymentSuccess($data),
                'subscription_created' => $this->handleSubscriptionCreated($data),
                'subscription_updated' => $this->handleSubscriptionUpdated($data),
                'subscription_cancelled' => $this->handleSubscriptionCancelled($data),
                'subscription_interval' => $this->handleSubscriptionInterval($data),
                'payment_failed' => $this->handlePaymentFailed($data),
                'payment_refunded' => $this->handlePaymentRefunded($data),
                default => Log::warning('Unhandled webhook event', ['event' => $eventType]),
            };

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error processing Fungies webhook', [
                'event' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle payment_success event
     */
    protected function handlePaymentSuccess(array $data): void
    {
        $userId = $data['metadata']['user_id'] ?? null;

        if (!$userId) {
            Log::error('Payment success webhook missing user_id in metadata');
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            Log::error('User not found for payment success', ['user_id' => $userId]);
            return;
        }

        // Update subscription status
        $user->update([
            'subscription_status' => 'active',
        ]);

        Log::info('Payment processed successfully', [
            'user_id' => $userId,
            'amount' => $data['amount'] ?? null,
        ]);

        // TODO: Send payment confirmation email
    }

    /**
     * Handle subscription_created event
     */
    protected function handleSubscriptionCreated(array $data): void
    {
        $userId = $data['metadata']['user_id'] ?? null;

        if (!$userId) {
            Log::error('Subscription created webhook missing user_id in metadata');
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            Log::error('User not found for subscription created', ['user_id' => $userId]);
            return;
        }

        // Update user with subscription details
        $user->update([
            'fungies_customer_id' => $data['customerId'] ?? null,
            'fungies_subscription_id' => $data['subscriptionId'] ?? $data['id'] ?? null,
            'subscription_status' => 'active',
            'subscription_current_period_start' => isset($data['currentPeriodStart'])
                ? now()->parse($data['currentPeriodStart'])
                : now(),
            'subscription_current_period_end' => isset($data['currentPeriodEnd'])
                ? now()->parse($data['currentPeriodEnd'])
                : now()->addMonth(),
            'trial_ends_at' => null, // Clear trial
            'cancel_at_period_end' => false,
        ]);

        Log::info('Subscription created successfully', [
            'user_id' => $userId,
            'subscription_id' => $user->fungies_subscription_id,
        ]);

        // TODO: Send welcome email
    }

    /**
     * Handle subscription_updated event
     */
    protected function handleSubscriptionUpdated(array $data): void
    {
        $subscriptionId = $data['subscriptionId'] ?? $data['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Subscription updated webhook missing subscription ID');
            return;
        }

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (!$user) {
            Log::error('User not found for subscription updated', [
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Update subscription details
        $updateData = [
            'subscription_status' => $data['status'] ?? $user->subscription_status,
        ];

        if (isset($data['currentPeriodStart'])) {
            $updateData['subscription_current_period_start'] = now()->parse($data['currentPeriodStart']);
        }

        if (isset($data['currentPeriodEnd'])) {
            $updateData['subscription_current_period_end'] = now()->parse($data['currentPeriodEnd']);
        }

        if (isset($data['cancelAtPeriodEnd'])) {
            $updateData['cancel_at_period_end'] = $data['cancelAtPeriodEnd'];
        }

        $user->update($updateData);

        Log::info('Subscription updated', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'updates' => $updateData,
        ]);
    }

    /**
     * Handle subscription_cancelled event
     */
    protected function handleSubscriptionCancelled(array $data): void
    {
        $subscriptionId = $data['subscriptionId'] ?? $data['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Subscription cancelled webhook missing subscription ID');
            return;
        }

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (!$user) {
            Log::error('User not found for subscription cancelled', [
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Update cancellation status
        $user->update([
            'subscription_status' => 'cancelled',
            'cancel_at_period_end' => true,
        ]);

        Log::info('Subscription cancelled', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'cancelled_at' => now(),
        ]);

        // TODO: Send cancellation confirmation email
    }

    /**
     * Handle subscription_interval event (renewal)
     */
    protected function handleSubscriptionInterval(array $data): void
    {
        $subscriptionId = $data['subscriptionId'] ?? $data['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Subscription interval webhook missing subscription ID');
            return;
        }

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (!$user) {
            Log::error('User not found for subscription interval', [
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Update billing period
        $user->update([
            'subscription_status' => 'active',
            'subscription_current_period_start' => isset($data['currentPeriodStart'])
                ? now()->parse($data['currentPeriodStart'])
                : now(),
            'subscription_current_period_end' => isset($data['currentPeriodEnd'])
                ? now()->parse($data['currentPeriodEnd'])
                : now()->addMonth(),
            'cancel_at_period_end' => false,
        ]);

        Log::info('Subscription renewed', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
        ]);

        // TODO: Send renewal confirmation email
    }

    /**
     * Handle payment_failed event
     */
    protected function handlePaymentFailed(array $data): void
    {
        $userId = $data['metadata']['user_id'] ?? null;
        $subscriptionId = $data['subscriptionId'] ?? null;

        $user = null;

        if ($userId) {
            $user = User::find($userId);
        } elseif ($subscriptionId) {
            $user = User::where('fungies_subscription_id', $subscriptionId)->first();
        }

        if (!$user) {
            Log::error('User not found for payment failed', [
                'user_id' => $userId,
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        Log::warning('Payment failed', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'reason' => $data['failureReason'] ?? 'Unknown',
        ]);

        // TODO: Send payment failed notification email
        // TODO: Implement grace period logic (e.g., 3 days)
        // TODO: Suspend account after multiple failures
    }

    /**
     * Handle payment_refunded event
     */
    protected function handlePaymentRefunded(array $data): void
    {
        $userId = $data['metadata']['user_id'] ?? null;

        if ($userId) {
            $user = User::find($userId);

            Log::info('Payment refunded', [
                'user_id' => $userId,
                'amount' => $data['amount'] ?? null,
            ]);

            // TODO: Send refund confirmation email
        }
    }
}
