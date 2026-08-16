<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('NARSIS_ADMIN_EMAIL', 'admin@narsis.test')],
            [
                'name' => env('NARSIS_ADMIN_NAME', 'Administrator Website'),
                'password' => env('NARSIS_ADMIN_PASSWORD', 'ChangeMe123!'),
                'role' => UserRole::WebsiteAdmin,
                'school_id' => null,
                'is_active' => true,
            ]
        );
    }
}
