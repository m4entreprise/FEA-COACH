<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LemonSqueezyService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $storeId;

    public function __construct()
    {
        $this->baseUrl = config('lemonsqueezy.base_url');
        $this->apiKey = (string) config('lemonsqueezy.api_key');
        $this->storeId = (string) config('lemonsqueezy.store_id');
    }

    /**
     * Create a checkout session on Lemon Squeezy.
     *
     * @param array $userData  Ex: ['user_id' => int, 'email' => string, 'name' => string, 'vat_number' => ?string]
     * @param array $customData Extra custom data stored in meta.custom_data
     * @param int|null $variantId  Variant ID (non-FEA or FEA). If null, chosen from is_fea_graduate flag.
     * @return array Decoded JSON response
     */
    public function createCheckoutSession(array $userData, array $customData = [], ?int $variantId = null): array
    {
        try {
            if ($variantId === null) {
                $isFea = (bool) ($customData['is_fea_graduate'] ?? false);
                $variantId = (int) ($isFea
                    ? config('lemonsqueezy.variant_fea')
                    : config('lemonsqueezy.variant_non_fea'));
            }

            // Lemon Squeezy requires all custom_data values to be strings
            $customDataStrings = [];
            foreach (array_merge(['user_id' => $userData['user_id'] ?? null], $customData) as $key => $value) {
                // Convert booleans explicitly to "true"/"false" strings
                if (is_bool($value)) {
                    $customDataStrings[$key] = $value ? 'true' : 'false';
                } else {
                    $customDataStrings[$key] = (string) $value;
                }
            }

            $checkoutData = [
                'email' => $userData['email'] ?? null,
                'name' => $userData['name'] ?? null,
                'custom' => $customDataStrings,
            ];

            // Only include tax_number if provided (Lemon Squeezy doesn't accept null)
            if (!empty($userData['vat_number'])) {
                $checkoutData['tax_number'] = $userData['vat_number'];
            }

            $payload = [
                'data' => [
                    'type' => 'checkouts',
                    'attributes' => [
                        'checkout_data' => $checkoutData,
                        'checkout_options' => [
                            'embed' => false,
                            'media' => true,
                            'logo' => true,
                            'desc' => true,
                            'discount' => true,
                            'locale' => 'fr',
                        ],
                        'product_options' => [
                            'enabled_variants' => [$variantId],
                            'redirect_url' => route('onboarding.step3'),
                        ],
                    ],
                    'relationships' => [
                        'store' => [
                            'data' => [
                                'type' => 'stores',
                                'id' => (string) $this->storeId,
                            ],
                        ],
                        'variant' => [
                            'data' => [
                                'type' => 'variants',
                                'id' => (string) $variantId,
                            ],
                        ],
                    ],
                ],
            ];

            // Log payload for debugging
            Log::info('Lemon Squeezy checkout payload', [
                'payload_json' => json_encode($payload, JSON_PRETTY_PRINT),
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/checkouts', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Lemon Squeezy checkout creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create Lemon Squeezy checkout session');
        } catch (\Exception $e) {
            Log::error('Lemon Squeezy API error', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get customer portal URL for a subscription.
     *
     * @param string $subscriptionId The Lemon Squeezy subscription ID
     * @return string|null The customer portal URL or null if not found
     */
    public function getCustomerPortalUrl(string $subscriptionId): ?string
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/subscriptions/' . $subscriptionId);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['attributes']['urls']['customer_portal'] ?? null;
            }

            Log::error('Failed to retrieve customer portal URL', [
                'subscription_id' => $subscriptionId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error retrieving customer portal URL', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get fallback customer portal URL (non-signed).
     *
     * @return string
     */
    public function getFallbackPortalUrl(): string
    {
        $storeDomain = config('lemonsqueezy.store_domain', 'unicoach.lemonsqueezy.com');
        return "https://{$storeDomain}/billing";
    }

    /**
     * Verify webhook signature from Lemon Squeezy.
     */
    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $signature = trim((string) $signature);
        if ($signature === '') {
            return false;
        }

        $secret = (string) config('lemonsqueezy.webhook_secret');
        if ($secret === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
