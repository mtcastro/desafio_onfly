<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
            'date' => $this->faker->date,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'user_id' => \App\Models\User::factory()->create()->id
        ];
    }
}
