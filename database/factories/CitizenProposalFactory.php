<?php

namespace Database\Factories;

use App\Models\CitizenProposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CitizenProposal>
 */
class CitizenProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_type' => fake()->randomElement(['Ordinance', 'Resolution']),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'proposer_name' => fake()->name(),
            'proposer_email' => fake()->safeEmail(),
            'proposer_phone' => fake()->phoneNumber(),
            'proposer_barangay' => 'Barangay '.fake()->numberBetween(1, 10),
            'status' => 'Pending',
        ];
    }
}
