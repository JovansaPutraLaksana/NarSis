<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@narsis.test',
            ],
            [
                'name' => 'Administrator Website',
                'password' => 'Password123!',
                'role' => UserRole::WebsiteAdmin,
            ]
        );
    }
}
