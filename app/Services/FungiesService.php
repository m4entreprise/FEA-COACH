<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FungiesService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $writeApiKey;
    protected string $planId;

    public function __construct()
    {
        $this->baseUrl = config('fungies.base_url');
        $this->apiKey = config('fungies.api_key');
        $this->writeApiKey = config('fungies.write_api_key');
        $this->planId = config('fungies.plan_id');
    }

    /**
     * Create a checkout session for subscription
     *
     * @param array $userData User data (email, name, etc.)
     * @param array $metadata Additional metadata
     * @param string|null $sku SKU to use (fea-coach-pro-graduate or fea-coach-pro-standard)
     * @return array Response from Fungies API
     */
    public function createCheckoutSession(array $userData, array $metadata = [], ?string $sku = null): array
    {
        try {
            // Determine SKU based on user type if not provided
            if ($sku === null) {
                $isFea = $metadata['is_fea_graduate'] ?? false;
                $sku = $isFea ? config('fungies.sku_fea') : config('fungies.sku_standard');
            }

            $orderData = [
                'planId' => $this->planId,
                'email' => $userData['email'],
                'name' => $userData['name'],
                'metadata' => array_merge([
                    'user_id' => $userData['user_id'],
                    'source' => 'fea-coach-platform',
                    'sku' => $sku,
                ], $metadata),
                'returnUrl' => config('fungies.checkout_success_url'),
                'cancelUrl' => config('fungies.checkout_cancel_url'),
            ];

            // Add SKU if provided (to select the correct variant)
            if ($sku) {
                $orderData['sku'] = $sku;
            }

            $response = Http::withHeaders([
                'x-write-api-key' => $this->writeApiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v0/elements/checkout/create", $orderData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Fungies checkout creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create checkout session');
        } catch (\Exception $e) {
            Log::error('Fungies API error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get subscription details by ID
     *
     * @param string $subscriptionId
     * @return array|null
     */
    public function getSubscription(string $subscriptionId): ?array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/v0/subscriptions/{$subscriptionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * List all subscriptions
     *
     * @param string $status Filter by status (active, canceled, all)
     * @param int $take Number of results to return
     * @return array
     */
    public function listSubscriptions(string $status = 'all', int $take = 100): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/v0/subscriptions/list", [
                'status' => $status,
                'take' => $take,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to list subscriptions', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Cancel a subscription
     *
     * @param string $subscriptionId
     * @param string $cancelOption 'immediately' or 'endPeriod'
     * @param string $refundOption 'noRefund', 'lastPayment', or 'prorate'
     * @return bool
     */
    public function cancelSubscription(
        string $subscriptionId,
        string $cancelOption = 'endPeriod',
        string $refundOption = 'noRefund'
    ): bool {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'x-write-api-key' => $this->writeApiKey,
                'Content-Type' => 'application/json',
            ])->patch("{$this->baseUrl}/v0/subscriptions/{$subscriptionId}/cancel", [
                'Id' => $subscriptionId,
                'cancelOption' => $cancelOption,
                'refundOption' => $refundOption,
            ]);

            if ($response->successful()) {
                Log::info('Subscription cancelled successfully', [
                    'subscription_id' => $subscriptionId,
                    'cancel_option' => $cancelOption,
                ]);
                return true;
            }

            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscriptionId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error cancelling subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload Raw webhook payload
     * @param string $signature Signature from webhook header
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('fungies.webhook_secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate customer portal URL
     * Note: Fungies.io may have a different implementation for customer portal
     * Check their documentation for the exact endpoint
     *
     * @param string $customerId
     * @return string|null
     */
    public function getCustomerPortalUrl(string $customerId): ?string
    {
        // This is a placeholder - check Fungies.io documentation
        // for the actual customer portal implementation
        return "https://fungies.io/portal/{$customerId}";
    }
}
