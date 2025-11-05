<?php

namespace Database\Factories;

use App\Models\Epreuve;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpreuveFactory extends Factory
{
    protected $model = Epreuve::class;

    public function definition(): array
    {
        return [
            'enseignant_id' => User::factory(),
            'titre' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'statut' => 'proposee',
        ];
    }
}
