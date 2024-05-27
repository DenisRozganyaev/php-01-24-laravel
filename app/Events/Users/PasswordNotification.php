<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class PasswordNotification
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(readonly public User $user, readonly public string $password)
    {
        //
    }
}
