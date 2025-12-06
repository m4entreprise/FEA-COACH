<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\User;
use App\Services\LemonSqueezyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LemonSqueezyWebhookController extends Controller
{
    protected LemonSqueezyService $service;

    public function __construct(LemonSqueezyService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle incoming Lemon Squeezy webhooks.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Signature');

        if (! $this->service->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Lemon Squeezy webhook signature');

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);

        if (! is_array($data)) {
            Log::error('Lemon Squeezy webhook invalid payload', ['payload' => $payload]);

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $eventName = $data['meta']['event_name'] ?? null;

        if (! $eventName) {
            Log::error('Lemon Squeezy webhook missing event name', ['data' => $data]);

            return response()->json(['error' => 'Missing event name'], 400);
        }

        Log::info('Lemon Squeezy webhook received', [
            'event' => $eventName,
            'data' => $data,
        ]);

        try {
            match ($eventName) {
                'subscription_created' => $this->handleSubscriptionCreated($data),
                'subscription_updated' => $this->handleSubscriptionUpdated($data),
                'subscription_cancelled' => $this->handleSubscriptionCancelled($data),
                'subscription_expired' => $this->handleSubscriptionExpired($data),
                default => Log::info('Unhandled Lemon Squeezy event', ['event' => $eventName]),
            };

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error('Error processing Lemon Squeezy webhook', [
                'event' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    protected function handleSubscriptionCreated(array $payload): void
    {
        $user = $this->resolveUserFromPayload($payload);

        if (! $user) {
            Log::error('User not found for Lemon Squeezy subscription_created', ['payload' => $payload]);

            return;
        }

        $data = $payload['data'] ?? [];
        $attributes = $data['attributes'] ?? [];
        $subscriptionId = (string) ($data['id'] ?? '');

        $update = [
            'fungies_customer_id' => $attributes['customer_id'] ?? null,
            'fungies_subscription_id' => $subscriptionId,
            'subscription_status' => $attributes['status'] ?? 'active',
            'cancel_at_period_end' => (bool) ($attributes['cancelled'] ?? false),
        ];

        if (! empty($attributes['trial_ends_at'])) {
            $update['trial_ends_at'] = now()->parse($attributes['trial_ends_at']);
        }

        if (! empty($attributes['renews_at'])) {
            $update['subscription_current_period_end'] = now()->parse($attributes['renews_at']);
            $update['subscription_current_period_start'] = now();
        }

        $user->update($update);

        $customData = $payload['meta']['custom_data'] ?? [];

        // Terminer l'onboarding et créer le profil coach si nécessaire
        if (! $user->onboarding_completed && ($customData['onboarding'] ?? false)) {
            $this->createCoachProfile($user);

            $user->onboarding_completed = true;
            $user->save();
        }

        Log::info('Lemon Squeezy subscription_created handled', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'status' => $user->subscription_status,
        ]);
    }

    protected function handleSubscriptionUpdated(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $attributes = $data['attributes'] ?? [];
        $subscriptionId = (string) ($data['id'] ?? '');

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (! $user) {
            $user = $this->resolveUserFromPayload($payload);
        }

        if (! $user) {
            Log::error('User not found for Lemon Squeezy subscription_updated', [
                'subscription_id' => $subscriptionId,
            ]);

            return;
        }

        $update = [];

        if (isset($attributes['status'])) {
            $update['subscription_status'] = $attributes['status'];
        }

        if (! empty($attributes['renews_at'])) {
            $update['subscription_current_period_end'] = now()->parse($attributes['renews_at']);
        }

        if (array_key_exists('cancelled', $attributes)) {
            $update['cancel_at_period_end'] = (bool) $attributes['cancelled'];
        }

        if (! empty($update)) {
            $user->update($update);
        }

        Log::info('Lemon Squeezy subscription_updated handled', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'updates' => $update,
        ]);
    }

    protected function handleSubscriptionCancelled(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $attributes = $data['attributes'] ?? [];
        $subscriptionId = (string) ($data['id'] ?? '');

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (! $user) {
            $user = $this->resolveUserFromPayload($payload);
        }

        if (! $user) {
            Log::error('User not found for Lemon Squeezy subscription_cancelled', [
                'subscription_id' => $subscriptionId,
            ]);

            return;
        }

        $user->update([
            'subscription_status' => $attributes['status'] ?? 'cancelled',
            'cancel_at_period_end' => true,
        ]);

        Log::info('Lemon Squeezy subscription_cancelled handled', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
        ]);
    }

    protected function handleSubscriptionExpired(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $subscriptionId = (string) ($data['id'] ?? '');

        $user = User::where('fungies_subscription_id', $subscriptionId)->first();

        if (! $user) {
            $user = $this->resolveUserFromPayload($payload);
        }

        if (! $user) {
            Log::error('User not found for Lemon Squeezy subscription_expired', [
                'subscription_id' => $subscriptionId,
            ]);

            return;
        }

        $user->update([
            'subscription_status' => 'expired',
            'cancel_at_period_end' => true,
        ]);

        Log::info('Lemon Squeezy subscription_expired handled', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
        ]);
    }

    protected function resolveUserFromPayload(array $payload): ?User
    {
        $meta = $payload['meta'] ?? [];
        $custom = $meta['custom_data'] ?? [];

        if (isset($custom['user_id'])) {
            $user = User::find($custom['user_id']);

            if ($user) {
                return $user;
            }
        }

        $attributes = $payload['data']['attributes'] ?? [];
        $email = $attributes['user_email'] ?? null;

        if ($email) {
            return User::where('email', $email)->first();
        }

        return null;
    }

    private function createCoachProfile(User $user): void
    {
        if ($user->coach_id) {
            return;
        }

        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $fullName = $fullName !== '' ? $fullName : ($user->name ?? 'Coach');

        $baseSlug = Str::slug($fullName);
        $slug = $baseSlug !== '' ? $baseSlug : 'coach';
        $counter = 1;

        while (Coach::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $coach = Coach::create([
            'name' => $fullName,
            'slug' => $slug,
            'primary_color' => '#9333ea',
            'secondary_color' => '#ec4899',
            'is_active' => true,
            'hero_title' => 'Transformez votre vie dès aujourd\'hui',
            'hero_subtitle' => 'Coaching personnalisé pour atteindre vos objectifs',
            'about_text' => 'Bienvenue ! Je suis ' . $fullName . ', votre coach sportif dédié.',
            'method_text' => 'Ma méthode repose sur un accompagnement personnalisé et adapté à vos besoins.',
            'cta_text' => 'Réserver ma séance découverte',
        ]);

        $user->coach_id = $coach->id;
        $user->save();
    }
}
