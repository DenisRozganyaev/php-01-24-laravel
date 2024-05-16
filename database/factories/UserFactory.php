<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
    // User::factory(5)->make() => create a collection with 5 obj User
    // User::factory(5)->create() => will create 5 users records in DB
    // User::factory(5, ['password' => 'test1234'])->create() => will create 5 users records in DB
    // User::factory(5, ['password' => 'test1234'])->make()

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->unique()->e164PhoneNumber(),
            'birthdate' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'password' => Hash::make(static::$password ??= 'password'), // static::$password = static::$password ?? 'password'
            'remember_token' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if (! $user->hasAnyRole(Roles::values())) {
                $user->assignRole(Roles::CUSTOMER->value);
            }
        });
    }

    // User::factory(10, ['email' => 'test@mail.com'])->create()
    public function withEmail(string $email)
    {
        return $this->state(fn (array $attrs) => ['email' => $email]);
    }
}
