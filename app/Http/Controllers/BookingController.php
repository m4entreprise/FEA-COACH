<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\ServiceType;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeConnectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected StripeConnectService $stripeService
    ) {}

    public function showCheckoutForm(Request $request, $serviceId)
    {
        $coach = app(Coach::class);

        $routeServiceId = $request->route('serviceId');
        if ($routeServiceId !== null) {
            $serviceId = $routeServiceId;
        }
        $serviceIdInt = (int) $serviceId;

        if (!optional($coach->user)->has_payments_module) {
            Log::warning('Checkout form blocked: payments module disabled', [
                'coach_id' => $coach->id ?? null,
                'serviceId_param' => $serviceId,
                'route_service_id' => $routeServiceId,
            ]);
            abort(404);
        }

        $serviceQuery = $coach->serviceTypes()
            ->where('is_active', true)
            ->where('booking_enabled', true)
            ->where('coach_id', $coach->id);

        Log::info('Checkout form lookup', [
            'coach_id' => $coach->id,
            'serviceId_param' => $serviceId,
            'route_service_id' => $routeServiceId,
            'serviceId' => $serviceIdInt,
            'eligible_service_ids' => (clone $serviceQuery)->pluck('id'),
        ]);

        $service = $serviceQuery->findOrFail($serviceIdInt);

        $formAction = $request->routeIs('coach.*')
            ? route('coach.booking.checkout', [
                'coach_slug' => $coach->slug,
                'serviceId' => $service->id,
            ])
            : route('booking.checkout.fallback', [
                'serviceId' => $service->id,
            ]);

        return view('coach-site.booking.checkout', [
            'coach' => $coach,
            'service' => $service,
            'formAction' => $formAction,
            'backUrl' => url()->previous(),
        ]);
    }

    public function directCheckout(Request $request)
    {
        $serviceId = $request->route('serviceId') ?? $request->route('service');

        Log::info('DirectCheckout called', [
            'coach_slug' => $request->route('coach_slug'),
            'service_id' => $serviceId,
            'request_data' => $request->all(),
        ]);
        
        $coach = app(Coach::class);
        if (!optional($coach->user)->has_payments_module) {
            abort(404);
        }

        $service = $coach->serviceTypes()
            ->where('is_active', true)
            ->where('booking_enabled', true)
            ->findOrFail($serviceId);
        
        $validated = $request->validate([
            'client_first_name' => 'required|string|max:100',
            'client_last_name' => 'required|string|max:100',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:30',
            'client_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $booking = $this->bookingService->createBooking([
                'service_type_id' => $service->id,
                'booking_date' => null,
                'start_time' => null,
                'client_first_name' => $validated['client_first_name'],
                'client_last_name' => $validated['client_last_name'],
                'client_email' => $validated['client_email'],
                'client_phone' => $validated['client_phone'] ?? null,
                'client_notes' => $validated['client_notes'] ?? null,
            ]);

            Log::info('Booking created', ['booking_id' => $booking->id]);

            $checkoutSession = $this->stripeService->createCheckoutSession($booking);

            Log::info('Checkout session created', ['url' => $checkoutSession['url']]);

            return redirect($checkoutSession['url']);
        } catch (\Exception $e) {
            Log::error('DirectCheckout failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Erreur lors de la création de la réservation: ' . $e->getMessage());
        }
    }

    public function success(Request $request, Booking $booking)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId && $booking->payment_status !== 'succeeded') {
            try {
                $booking->load(['coach.stripeAccount']);
                $stripeAccountId = $booking->coach?->stripeAccount?->stripe_account_id;

                if ($stripeAccountId) {
                    $session = $this->stripeService->retrieveCheckoutSession($sessionId, $stripeAccountId);
                    $sessionBookingId = $session['metadata']['booking_id'] ?? null;

                    if ((string) $sessionBookingId === (string) $booking->id && ($session['payment_status'] ?? null) === 'paid') {
                        $paymentIntentId = $session['payment_intent']['id'] ?? ($session['payment_intent'] ?? null);
                        if ($paymentIntentId) {
                            $this->bookingService->confirmBooking($booking, $paymentIntentId);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Booking success fallback confirmation failed', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking->refresh();
        $booking->load(['serviceType', 'coach', 'client']);

        $clientShareLink = null;
        $clientShareCode = null;
        if ($booking->client && $booking->client->share_token) {
            $clientShareCode = $booking->client->share_code;
            $clientShareLink = route('clients.share.show', $booking->client->share_token);
        }

        return Inertia::render('Booking/Success', [
            'booking' => [
                'id' => $booking->id,
                'booking_date' => $booking->booking_date?->format('d/m/Y'),
                'start_time' => $booking->start_time ? substr($booking->start_time, 0, 5) : null,
                'end_time' => $booking->end_time ? substr($booking->end_time, 0, 5) : null,
                'service_name' => $booking->serviceType->name,
                'coach_name' => $booking->coach->name,
                'amount' => $booking->formatted_amount,
                'client_email' => $booking->client_email,
                'client_share_code' => $clientShareCode,
                'client_share_link' => $clientShareLink,
            ],
        ]);
    }

    public function cancel(Booking $booking)
    {
        return Inertia::render('Booking/Cancel', [
            'booking' => [
                'id' => $booking->id,
            ],
        ]);
    }
}
