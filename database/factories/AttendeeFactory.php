<?php

namespace Database\Factories;

use App\Enums\RsvpStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendee>
 */
class AttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'rsvp_status' => $this->faker->randomElement([
                RsvpStatus::Pending->value,
                RsvpStatus::Accepted->value,
                RsvpStatus::Declined->value
            ]),
        ];
    }
}
