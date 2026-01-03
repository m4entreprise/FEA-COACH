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

    public function directCheckout(Request $request, $service)
    {
        $coach = app(Coach::class);
        $service = ServiceType::findOrFail($service);
        
        $validated = $request->validate([
            'client_email' => 'required|email|max:255',
        ]);

        try {
            $booking = $this->bookingService->createBooking([
                'service_type_id' => $service->id,
                'booking_date' => null,
                'start_time' => null,
                'client_email' => $validated['client_email'],
                'client_first_name' => null,
                'client_last_name' => null,
                'client_phone' => null,
                'client_notes' => null,
            ]);

            $checkoutSession = $this->stripeService->createCheckoutSession($booking);

            return redirect($checkoutSession['url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création de la réservation: ' . $e->getMessage());
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
