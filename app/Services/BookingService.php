<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coach;
use App\Models\ServiceType;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function isSlotAvailable(
        Coach $coach,
        Carbon $date,
        string $startTime,
        int $durationMinutes
    ): bool {
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = $start->copy()->addMinutes($durationMinutes);

        $existingBooking = Booking::where('coach_id', $coach->id)
            ->where('booking_date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $end) {
                $endTime = $end->format('H:i:s');
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$existingBooking;
    }

    public function createBooking(array $data): Booking
    {
        try {
            DB::beginTransaction();

            $serviceType = ServiceType::findOrFail($data['service_type_id']);
            $coach = $serviceType->coach;

            // Handle bookings with or without specific date/time
            $bookingDate = null;
            $startTime = null;
            $endTime = null;

            if (!empty($data['booking_date']) && !empty($data['start_time'])) {
                $bookingDate = Carbon::parse($data['booking_date']);
                $startTime = $data['start_time'];
                $endTime = Carbon::parse($bookingDate->format('Y-m-d') . ' ' . $startTime)
                    ->addMinutes($serviceType->duration_minutes)
                    ->format('H:i:s');

                if (!$this->isSlotAvailable($coach, $bookingDate, $startTime, $serviceType->duration_minutes)) {
                    throw new \Exception('Ce créneau n\'est plus disponible');
                }
            }

            $clientId = null;
            if (!empty($data['client_email'])) {
                $client = Client::where('coach_id', $coach->id)
                    ->where('email', $data['client_email'])
                    ->first();

                if ($client) {
                    $clientId = $client->id;
                } else {
                    $client = Client::create([
                        'coach_id' => $coach->id,
                        'first_name' => $data['client_first_name'] ?? 'Client',
                        'last_name' => $data['client_last_name'] ?? 'Invité',
                        'email' => $data['client_email'],
                        'phone' => $data['client_phone'] ?? null,
                    ]);
                    $clientId = $client->id;
                }
            }

            $booking = Booking::create([
                'coach_id' => $coach->id,
                'client_id' => $clientId,
                'service_type_id' => $serviceType->id,
                'client_first_name' => $data['client_first_name'] ?? null,
                'client_last_name' => $data['client_last_name'] ?? null,
                'client_email' => $data['client_email'],
                'client_phone' => $data['client_phone'] ?? null,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $serviceType->duration_minutes,
                'amount' => $serviceType->price,
                'currency' => $serviceType->currency,
                'status' => 'pending',
                'payment_status' => 'pending',
                'client_notes' => $data['client_notes'] ?? null,
            ]);

            DB::commit();

            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create booking', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    public function getAvailableSlots(Coach $coach, Carbon $date, ServiceType $serviceType): array
    {
        $dayOfWeek = $date->dayOfWeek;
        
        $availabilitySlots = $coach->availabilitySlots()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($availabilitySlots->isEmpty()) {
            return [];
        }

        $availableSlots = [];
        $slotDuration = $serviceType->duration_minutes;

        foreach ($availabilitySlots as $slot) {
            $slotStart = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->start_time);
            $slotEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->end_time);

            $current = $slotStart->copy();
            while ($current->copy()->addMinutes($slotDuration)->lte($slotEnd)) {
                $proposedStart = $current->format('H:i:s');
                
                if ($this->isSlotAvailable($coach, $date, $proposedStart, $slotDuration)) {
                    $minAdvance = Carbon::now()->addHours($serviceType->min_advance_booking_hours);
                    $slotDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $proposedStart);
                    
                    if ($slotDateTime->gt($minAdvance)) {
                        $availableSlots[] = [
                            'time' => substr($proposedStart, 0, 5),
                            'datetime' => $slotDateTime->toIso8601String(),
                        ];
                    }
                }
                
                $current->addMinutes(30);
            }
        }

        return $availableSlots;
    }

    public function confirmBooking(Booking $booking, ?string $paymentIntentId): void
    {
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'succeeded',
            'stripe_payment_intent_id' => $paymentIntentId,
            'paid_at' => now(),
        ]);
    }

    public function cancelBooking(Booking $booking, string $reason, string $cancelledBy = 'client'): void
    {
        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy,
        ]);
    }

    public function getUpcomingBookings(Coach $coach)
    {
        return $coach->bookings()
            ->with(['serviceType', 'client'])
            ->upcoming()
            ->get();
    }

    public function getBookingStats(Coach $coach): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_bookings' => $coach->bookings()->count(),
            'total_revenue' => $coach->bookings()
                ->where('payment_status', 'succeeded')
                ->sum('amount'),
            'this_month_bookings' => $coach->bookings()
                ->where('paid_at', '>=', $thisMonth)
                ->where('payment_status', 'succeeded')
                ->count(),
            'this_month_revenue' => $coach->bookings()
                ->where('paid_at', '>=', $thisMonth)
                ->where('payment_status', 'succeeded')
                ->sum('amount'),
            'upcoming_bookings' => $coach->bookings()
                ->upcoming()
                ->count(),
            'completion_rate' => $this->calculateCompletionRate($coach),
        ];
    }

    protected function calculateCompletionRate(Coach $coach): float
    {
        $total = $coach->bookings()
            ->whereIn('status', ['completed', 'no_show', 'cancelled'])
            ->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $coach->bookings()
            ->where('status', 'completed')
            ->count();

        return round(($completed / $total) * 100, 1);
    }
}
