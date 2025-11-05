<?php

namespace Database\Factories;

use App\Models\Td;
use App\Models\Epreuve;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TdFactory extends Factory
{
    protected $model = Td::class;

    public function definition(): array
    {
        return [
            'epreuve_id' => Epreuve::factory(),
            'enseignant_id' => User::factory(),
            'titre' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'statut' => fake()->randomElement(['en_attente','en_cours','termine','paye']),
            'montant' => fake()->randomFloat(2, 10000, 100000),
            'date_debut' => now()->subDays(5),
            'date_fin' => now(),
        ];
    }
}
