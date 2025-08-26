<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Revenue>
 */
class RevenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'fonte_id' => Category::factory()->fonte(),
            'bloco_id' => Category::factory()->bloco(),
            'grupo_id' => Category::factory()->grupo(),
            'acao_id' => Category::factory()->acao(),
            'observation' => $this->faker->optional(0.3)->paragraph(),
        ];
    }
}
