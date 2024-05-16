<?php

namespace Tests\Feature\Traits;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait SetupTrait
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function afterRefreshingDatabase()
    {
        $this->seed(PermissionsAndRolesSeeder::class);
        $this->seed(UserSeeder::class);
    }

    protected function user(Roles $role = Roles::ADMIN): User
    {
        return User::role($role->value)->firstOrFail();
    }
}
