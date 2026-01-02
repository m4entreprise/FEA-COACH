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

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected StripeConnectService $stripeService
    ) {}

    public function show(Coach $coach)
    {
        $coach->load(['serviceTypes' => function ($query) {
            $query->where('is_active', true)
                ->where('booking_enabled', true)
                ->orderBy('order');
        }]);

        $stripeAccount = $coach->stripeAccount;
        $canAcceptBookings = $stripeAccount && $stripeAccount->canAcceptPayments();

        return Inertia::render('Booking/Index', [
            'coach' => [
                'id' => $coach->id,
                'name' => $coach->name,
                'subdomain' => $coach->subdomain,
            ],
            'services' => $coach->serviceTypes,
            'canAcceptBookings' => $canAcceptBookings,
            'stripePublicKey' => config('stripe.public_key'),
        ]);
    }

    public function availableSlots(Request $request, ServiceType $service)
    {
        $coach = app(Coach::class);
        
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = Carbon::parse($validated['date']);
        $slots = $this->bookingService->getAvailableSlots($coach, $date, $service);

        return response()->json([
            'slots' => $slots,
        ]);
    }

    public function create(Request $request, ServiceType $service)
    {
        $coach = app(Coach::class);
        
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        $date = Carbon::parse($validated['date']);
        $slots = $this->bookingService->getAvailableSlots($coach, $date, $service);

        $isSlotAvailable = collect($slots)->contains('time', $validated['time']);

        if (!$isSlotAvailable) {
            return back()->with('error', 'Ce créneau n\'est plus disponible');
        }

        return Inertia::render('Booking/Create', [
            'coach' => [
                'id' => $coach->id,
                'name' => $coach->name,
                'subdomain' => $coach->subdomain,
            ],
            'service' => $service,
            'selectedDate' => $validated['date'],
            'selectedTime' => $validated['time'],
        ]);
    }

    public function store(Request $request, ServiceType $service)
    {
        $coach = app(Coach::class);
        
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:50',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $booking = $this->bookingService->createBooking([
                'service_type_id' => $service->id,
                ...$validated,
            ]);

            $checkoutSession = $this->stripeService->createCheckoutSession($booking);

            return response()->json([
                'checkout_url' => $checkoutSession['url'],
                'session_id' => $checkoutSession['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function success(Request $request, Booking $booking)
    {
        $booking->load(['serviceType', 'coach']);

        return Inertia::render('Booking/Success', [
            'booking' => [
                'id' => $booking->id,
                'booking_date' => $booking->booking_date->format('d/m/Y'),
                'start_time' => substr($booking->start_time, 0, 5),
                'end_time' => substr($booking->end_time, 0, 5),
                'service_name' => $booking->serviceType->name,
                'coach_name' => $booking->coach->name,
                'amount' => $booking->formatted_amount,
                'client_email' => $booking->client_email,
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
