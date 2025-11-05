<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que le cache des permissions est vidé avant modifications
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['admin','enseignant','comptable'];
        foreach ($roles as $name) {
            // Corrige d'éventuelles anciennes entrées avec mauvais guard (ex: 'sanctum')
            $existing = Role::where('name', $name)->first();
            if ($existing) {
                if ($existing->guard_name !== config('auth.defaults.guard', 'web')) {
                    $existing->guard_name = config('auth.defaults.guard', 'web');
                    $existing->save();
                }
            } else {
                Role::create(['name' => $name, 'guard_name' => config('auth.defaults.guard', 'web')]);
            }
        }

    if (!User::where('email','admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'is_validated' => true,
                'role' => 'admin',
            ]);
            $admin->assignRole('admin');
        }

    // Reflush cache après création
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
