<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->optional()->numerify('###'),
            'type' => 'fonte',
            'active' => true,
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the category is a fonte.
     */
    public function fonte(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_FONTE,
            'parent_id' => null,
        ]);
    }

    /**
     * Indicate that the category is a bloco.
     */
    public function bloco(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_BLOCO,
        ]);
    }

    /**
     * Indicate that the category is a grupo.
     */
    public function grupo(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_GRUPO,
        ]);
    }

    /**
     * Indicate that the category is an acao.
     */
    public function acao(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Category::TYPE_ACAO,
        ]);
    }
}
