<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingsController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard');
        }

        $bookings = $coach->bookings()
            ->with(['serviceType', 'client'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return Inertia::render('Coach/BookingsBeta', [
            'bookings' => $bookings,
            'stats' => $this->bookingService->getBookingStats($coach),
        ]);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['serviceType', 'client']);

        return Inertia::render('Dashboard/BookingDetails', [
            'booking' => $booking,
        ]);
    }

    public function updateNotes(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'coach_notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Notes mises à jour');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée');
        }

        $this->bookingService->cancelBooking($booking, $validated['reason'], 'coach');

        return back()->with('success', 'Réservation annulée');
    }

    public function markCompleted(Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->isConfirmed() && !$booking->isPending()) {
            return back()->with('error', 'Cette réservation ne peut pas être marquée comme complétée');
        }

        $booking->update(['status' => 'completed']);

        return back()->with('success', 'Réservation marquée comme complétée');
    }

    public function markNoShow(Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->isConfirmed() && !$booking->isPending()) {
            return back()->with('error', 'Cette réservation ne peut pas être marquée comme absence');
        }

        $booking->update(['status' => 'no_show']);

        return back()->with('success', 'Client marqué comme absent');
    }
}
