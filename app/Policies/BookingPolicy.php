<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->coach !== null;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->coach && $user->coach->id === $booking->coach_id;
    }

    public function create(User $user): bool
    {
        return $user->coach !== null && $user->has_payments_module;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->coach && $user->coach->id === $booking->coach_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->coach && $user->coach->id === $booking->coach_id;
    }
}
