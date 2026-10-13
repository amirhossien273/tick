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
    public function configure(): static
    {
        return $this->afterMaking(function (System $system): void {
            $system->api_key_hash = System::hashApiKey($system->api_key);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $apiKey = 'tkt_'.Str::random(56);

        return [
            'name' => fake()->company(),
            'description' => fake()->optional()->sentence(),
            'api_key' => $apiKey,
            'api_key_hash' => System::hashApiKey($apiKey),
            'is_active' => true,
            'last_used_at' => null,
            'created_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
