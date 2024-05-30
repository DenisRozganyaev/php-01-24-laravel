<?php

namespace App\Http\Controllers\Callbacks;

use App\Enums\Roles;
use App\Events\Users\PasswordNotification;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $data = Socialite::driver('google')->stateless()->user();
        $email = $data->offsetGet('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $password = Str::password(rand(8, 16));
            $user = User::create([
                'name' => $data->offsetGet('given_name'),
                'lastname' => $data->offsetGet('family_name'),
                'email' => $email,
                'phone' => null,
                'birthdate' => null,
                'password' => Hash::make($password),
            ]);

            $user->assignRole(Roles::CUSTOMER);
            PasswordNotification::dispatch($user, $password);
        }

        auth()->login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
