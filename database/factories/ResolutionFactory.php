<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resolution>
 */
class ResolutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'resolution_number' => 'RES-'.fake()->year().'-'.fake()->numberBetween(10, 99),
            'stage' => 'Final',
            'version_number' => '1.0',
            'author' => fake()->name(),
            'purpose' => fake()->sentence(),
            'date_prepared' => now()->format('Y-m-d'),
            'encoded_by' => User::factory(),
        ];
    }
}
