<?php

namespace App\Policies;

use App\Models\AvailabilitySlot;
use App\Models\User;

class AvailabilitySlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->coach !== null;
    }

    public function view(User $user, AvailabilitySlot $slot): bool
    {
        return $user->coach && $user->coach->id === $slot->coach_id;
    }

    public function create(User $user): bool
    {
        return $user->coach !== null && $user->has_payments_module;
    }

    public function update(User $user, AvailabilitySlot $slot): bool
    {
        return $user->coach && $user->coach->id === $slot->coach_id;
    }

    public function delete(User $user, AvailabilitySlot $slot): bool
    {
        return $user->coach && $user->coach->id === $slot->coach_id;
    }
}
