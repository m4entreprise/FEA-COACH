<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Booking;
use App\Models\StripeAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeConnectService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->apiKey = (string) config('stripe.secret_key');
    }

    public function createConnectedAccount(Coach $coach): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->asForm()->post($this->baseUrl . '/accounts', [
                'type' => 'express',
                'email' => $coach->user->email,
                'capabilities[card_payments][requested]' => 'true',
                'capabilities[transfers][requested]' => 'true',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to create Stripe connected account', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create Stripe connected account');
        } catch (\Exception $e) {
            Log::error('Stripe Connect account creation error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createAccountLink(Coach $coach, string $accountId): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->asForm()->post($this->baseUrl . '/account_links', [
                'account' => $accountId,
                'refresh_url' => route('dashboard.payments.stripe.refresh'),
                'return_url' => route('dashboard.payments.stripe.return'),
                'type' => 'account_onboarding',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['url'];
            }

            Log::error('Failed to create Stripe account link', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create account link');
        } catch (\Exception $e) {
            Log::error('Stripe account link error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getOrCreateAccountLink(Coach $coach): string
    {
        $stripeAccount = $coach->stripeAccount;

        if (!$stripeAccount) {
            $account = $this->createConnectedAccount($coach);
            $stripeAccount = StripeAccount::create([
                'coach_id' => $coach->id,
                'stripe_account_id' => $account['id'],
                'country' => $account['country'] ?? 'FR',
                'business_type' => $account['business_type'] ?? 'individual',
            ]);
        }

        return $this->createAccountLink($coach, $stripeAccount->stripe_account_id);
    }

    public function retrieveAccount(string $accountId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/accounts/' . $accountId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to retrieve Stripe account', [
                'account_id' => $accountId,
                'status' => $response->status(),
            ]);

            throw new \Exception('Failed to retrieve account');
        } catch (\Exception $e) {
            Log::error('Stripe retrieve account error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function updateStripeAccountStatus(StripeAccount $stripeAccount): void
    {
        $accountData = $this->retrieveAccount($stripeAccount->stripe_account_id);

        $stripeAccount->update([
            'charges_enabled' => $accountData['charges_enabled'] ?? false,
            'payouts_enabled' => $accountData['payouts_enabled'] ?? false,
            'details_submitted' => $accountData['details_submitted'] ?? false,
            'onboarding_completed' => ($accountData['details_submitted'] ?? false) 
                && ($accountData['charges_enabled'] ?? false),
        ]);
    }

    public function createPaymentIntent(Booking $booking): array
    {
        $coach = $booking->coach;
        $stripeAccount = $coach->stripeAccount;

        if (!$stripeAccount || !$stripeAccount->canAcceptPayments()) {
            throw new \Exception('Coach Stripe account not ready to accept payments');
        }

        $amount = (int) ($booking->amount * 100);
        $platformFee = $this->calculatePlatformFee($amount);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Stripe-Account' => $stripeAccount->stripe_account_id,
            ])->asForm()->post($this->baseUrl . '/payment_intents', [
                'amount' => $amount,
                'currency' => strtolower($booking->currency),
                'application_fee_amount' => $platformFee,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'coach_id' => $coach->id,
                    'service_type_id' => $booking->service_type_id,
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to create payment intent', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create payment intent');
        } catch (\Exception $e) {
            Log::error('Stripe payment intent error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createCheckoutSession(Booking $booking): array
    {
        $coach = $booking->coach;
        $stripeAccount = $coach->stripeAccount;

        if (!$stripeAccount || !$stripeAccount->canAcceptPayments()) {
            throw new \Exception('Coach Stripe account not ready to accept payments');
        }

        $amount = (int) ($booking->amount * 100);
        $platformFee = $this->calculatePlatformFee($amount);

        $description = 'Séance à planifier';
        if ($booking->booking_date && $booking->start_time) {
            $description = 'Séance du ' . $booking->booking_date->format('d/m/Y') . ' à ' . substr($booking->start_time, 0, 5);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->asForm()->post($this->baseUrl . '/checkout/sessions', [
                'mode' => 'payment',
                'payment_intent_data' => [
                    'application_fee_amount' => $platformFee,
                    'transfer_data' => [
                        'destination' => $stripeAccount->stripe_account_id,
                    ],
                ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($booking->currency),
                            'product_data' => [
                                'name' => $booking->serviceType->name,
                                'description' => $description,
                            ],
                            'unit_amount' => $amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'success_url' => route('booking.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('booking.cancel', ['booking' => $booking->id]),
                'customer_email' => $booking->client_email,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'coach_id' => $coach->id,
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to create checkout session', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to create checkout session');
        } catch (\Exception $e) {
            Log::error('Stripe checkout session error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function calculatePlatformFee(int $amount): int
    {
        $commissionRate = (float) config('stripe.platform_commission_rate', 0.0);
        return (int) ($amount * $commissionRate);
    }

    public function getDashboardLink(string $accountId): string
    {
        try {
            Log::info('Tentative génération lien dashboard Stripe', [
                'account_id' => $accountId,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->asForm()->post($this->baseUrl . '/accounts/' . $accountId . '/login_links');

            Log::info('Réponse Stripe login_links', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Lien dashboard généré', ['url' => $data['url']]);
                return $data['url'];
            }

            Log::error('Échec génération lien dashboard Stripe', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to generate Stripe dashboard link: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Exception génération lien dashboard', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
