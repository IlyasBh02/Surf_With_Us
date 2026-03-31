<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ilyas.admin@gmail.com'],
            [
                'name'           => 'Ilyas Admin',
                'password'       => Hash::make('246813579'),
                'role'           => 'admin',
                'status'         => 'active',
                'coach_approved' => true,
            ]
        );
    }
}
