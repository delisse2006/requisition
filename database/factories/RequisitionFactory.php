<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requisition>
 */
class RequisitionFactory extends Factory
{
    public function definition(): array
    {
        $items = [
            'Laptop', 'Mouse', 'Keyboard', 'Monitor', 'Printer', 'Paper Ream',
            'Desk Chair', 'Whiteboard', 'Projector', 'USB Drive', 'Headphones'
        ];

        $descriptions = [
            'For new team member onboarding',
            'Replacement for damaged unit',
            'Department upgrade',
            'Conference room setup',
            'Remote work equipment'
        ];

        return [
            'user_id' => User::factory(),
            'item_name' => $this->faker->randomElement($items),
            'description' => $this->faker->sentence() . ' ' . $this->faker->randomElement($descriptions),
            'quantity' => $this->faker->numberBetween(1, 10),
            'urgency' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['pending', 'bought', 'done', 'paid']),
            'notes' => $this->faker->optional(0.7)->sentence(), // 70% chance of having notes
            'received_confirmed' => $this->faker->boolean(30), // 30% confirmed
        ];
    }
}
