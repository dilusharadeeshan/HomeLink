<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = mb_strtolower(trim((string) env('INITIAL_ADMIN_EMAIL')));

        if ($email === '') {
            throw new RuntimeException('Set INITIAL_ADMIN_EMAIL before running the admin seeder.');
        }

        $admin = User::where('email', $email)->first();

        if ($admin !== null) {
            $admin->forceFill(['role' => 'admin', 'is_active' => true])->save();
            $this->command?->info('Existing account promoted to admin.');

            return;
        }

        $password = (string) env('INITIAL_ADMIN_PASSWORD');

        if ($password === '') {
            throw new RuntimeException('Set INITIAL_ADMIN_PASSWORD before creating the initial admin.');
        }

        User::create([
            'name' => trim((string) env('INITIAL_ADMIN_NAME', 'HomeLink Admin')),
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command?->info('Initial admin account created.');
    }
}
