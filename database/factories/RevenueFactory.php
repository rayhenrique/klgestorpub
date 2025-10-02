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
        // Criar hierarquia válida
        $fonte = Category::factory()->fonte()->create();
        $bloco = Category::factory()->bloco()->create(['parent_id' => $fonte->id]);
        
        return [
            'description' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => null,
            'acao_id' => null,
            'observation' => $this->faker->optional(0.3)->paragraph(),
        ];
    }
}
