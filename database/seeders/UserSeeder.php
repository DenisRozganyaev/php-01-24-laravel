<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    const ADMIN_EMAIL = 'admin@admin.com';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        if (! User::where('email', self::ADMIN_EMAIL)->exists()) {
            (User::factory()->withEmail(self::ADMIN_EMAIL)->create())
                ->syncRoles(Roles::ADMIN->value);
        }

        (User::factory(1)->create())->first()->syncRoles(Roles::MODERATOR->value);

        User::factory(5)->create(['password' => 'test1234']);
    }
}
