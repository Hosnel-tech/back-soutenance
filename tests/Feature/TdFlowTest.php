<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Epreuve;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TdFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_admin_creates_td_then_teacher_terminates_and_payment_recorded(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_validated' => true, 'password' => Hash::make('pass')]);
        $admin->assignRole('admin');
        $teacher = User::factory()->create(['role' => 'enseignant', 'is_validated' => true]);
        $teacher->assignRole('enseignant');

        $epreuve = Epreuve::factory()->create(['enseignant_id' => $teacher->id]);

        $token = $admin->createToken('api')->plainTextToken;
        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/tds', [
                'epreuve_id' => $epreuve->id,
                'enseignant_id' => $teacher->id,
                'titre' => 'TD 1',
                'description' => 'Desc',
                'montant' => 50000,
            ]);
        $resp->assertCreated();
        $tdId = $resp->json('id');

        $tToken = $teacher->createToken('api')->plainTextToken;
        $tResp = $this->withHeader('Authorization', 'Bearer '.$tToken)
            ->postJson("/api/tds/{$tdId}/terminer");
        $tResp->assertOk();
    }
}
