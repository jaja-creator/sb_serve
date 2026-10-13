<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'series_number' => 'SB-'.fake()->year().'-'.str_pad((string) fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'title' => fake()->sentence(8),
            'document_type' => fake()->randomElement(['Ordinance', 'Resolution']),
            'subject_matter' => fake()->words(3, true),
            'sponsor' => fake()->name(),
            'status' => 'Filed',
            'date_filed' => now()->subDays(fake()->numberBetween(1, 180))->format('Y-m-d'),
            'encoded_by' => User::factory(),
        ];
    }
}
