<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionsAndRolesSeeder::class);
        if (!app()->environment('production')) {
            $this->call(UserSeeder::class);
            $this->call(CategoryProductSeeder::class);
        }
    }
}
