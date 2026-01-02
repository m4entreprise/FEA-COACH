<?php

namespace App\Policies;

use App\Models\ServiceType;
use App\Models\User;

class ServiceTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->coach !== null;
    }

    public function view(User $user, ServiceType $service): bool
    {
        return $user->coach && $user->coach->id === $service->coach_id;
    }

    public function create(User $user): bool
    {
        return $user->coach !== null && $user->has_payments_module;
    }

    public function update(User $user, ServiceType $service): bool
    {
        return $user->coach && $user->coach->id === $service->coach_id;
    }

    public function delete(User $user, ServiceType $service): bool
    {
        return $user->coach && $user->coach->id === $service->coach_id;
    }
}
