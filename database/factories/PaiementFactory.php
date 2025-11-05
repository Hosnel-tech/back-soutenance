<?php

namespace Database\Factories;

use App\Models\Paiement;
use App\Models\Td;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'td_id' => Td::factory(),
            'montant' => fake()->randomFloat(2, 10000, 100000),
            'banque' => fake()->randomElement(['SGB', 'BICICI', 'UBA', 'NSIA']),
            'reference' => strtoupper(fake()->bothify('REF####')),
            'date_paiement' => now(),
        ];
    }
}
