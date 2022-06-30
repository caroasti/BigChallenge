<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */

/**
 * @mixin User
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'symptoms' => $this->faker->paragraph,
            'patient_id' => User::factory()->patient(),
            'state' => Submission::STATUS_PENDING,
        ];
    }

    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'state' => Submission::STATUS_IN_PROGRESS,
                'doctor_id' => User::factory()->doctor(),
            ];
        });
    }

    public function ready()
    {
        return $this->state(function (array $attributes) {
            return [
                'state' => Submission::STATUS_DONE,
                'doctor_id' => User::factory()->doctor(),
                'prescriptions' => $this->faker->paragraph,
            ];
        });
    }
}
