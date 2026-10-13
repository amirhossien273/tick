<?php

namespace Database\Factories;

use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<System>
 */
class SystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->bothify('SYS-#####-???'),
            'description' => fake()->optional()->sentence(),
            'api_key' => Str::random(64),
            'is_active' => true,
            'last_used_at' => null,
            'created_by' => User::factory(),
        ];
    }
}
