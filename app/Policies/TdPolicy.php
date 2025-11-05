<?php

namespace App\Policies;

use App\Models\Td;
use App\Models\User;

class TdPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Td $td): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('enseignant')) return $td->enseignant_id === $user->id;
        return false;
    }
}
