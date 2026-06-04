<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@publishin.ahdirmai.id');
        $password = env('ADMIN_PASSWORD', 'changeme123');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Admin',
                'password'          => $password, // cast 'hashed' on model auto-hashes
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info("Admin user ready: {$email}");
        $this->command->warn('Change password immediately after login!');
    }
}
