<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@safevoice.org'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::SUPER_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // 2. Admin
        User::updateOrCreate(
            ['email' => 'admin@safevoice.org'],
            [
                'name' => 'Compliance Manager',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // 3. Lead Investigator
        User::updateOrCreate(
            ['email' => 'investigator@safevoice.org'],
            [
                'name' => 'Lead Investigator',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::INVESTIGATOR,
                'email_verified_at' => now(),
            ]
        );
    }
}
