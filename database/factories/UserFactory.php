<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement([
                UserRole::Admin->value,
                UserRole::Organizer->value,
                UserRole::Attendee->value,
            ]),
        ];
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::Admin->value,
            ];
        });
    }

    public function organizer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::Organizer->value,
            ];
        });
    }

    public function attendee(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::Attendee->value,
            ];
        });
    }
}
