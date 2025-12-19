<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeSuggestion>
 */
class KnowledgeSuggestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'answer' => $this->faker->paragraph(),
            'source' => $this->faker->randomElement(['api', 'manual', 'chat']),
        ];
    }
}
