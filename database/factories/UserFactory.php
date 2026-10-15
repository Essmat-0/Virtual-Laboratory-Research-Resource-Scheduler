<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * The application's `users` table is customized: it has no
     * `email_verified_at` column, and `role_id` / `expiry_date` are NOT NULL.
     * This factory mirrors that schema instead of the stock Laravel default.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            // Creating the role without events avoids the global audit-trail
            // listener writing a user_id=1 row before any user exists (FK error).
            'role_id' => Role::withoutEvents(fn () => Role::firstOrCreate(
                ['name' => 'Researcher'],
                ['id' => User::ROLE_RESEARCHER],
            )->id),
            'clearance_level' => User::GRAD,
            'is_active' => true,
            'expiry_date' => now()->addYear(),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ];
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }
}
