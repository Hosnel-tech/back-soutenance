<?php

namespace App\Policies;

use App\Models\Epreuve;
use App\Models\User;

class EpreuvePolicy
{
    public function view(User $user, Epreuve $epreuve): bool
    {
        return $user->hasRole('admin') || $epreuve->enseignant_id === $user->id;
    }

    public function update(User $user, Epreuve $epreuve): bool
    {
        return $this->view($user, $epreuve);
    }

    public function delete(User $user, Epreuve $epreuve): bool
    {
        if ($epreuve->statut === 'validee') {
            return $user->hasRole('admin');
        }
        return $this->view($user, $epreuve);
    }
}
