<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_login_teacher_flow(): void
    {
    $this->seed(\Database\Seeders\RoleSeeder::class);
        $resp = $this->postJson('/api/auth/register', [
            'name' => 'Jean',
            'email' => 'jean@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'enseignant',
        ]);
        $resp->assertCreated();

        $login = $this->postJson('/api/auth/login', [
            'email' => 'jean@example.com',
            'password' => 'password123',
        ]);
        $login->assertStatus(403);
    }
}
