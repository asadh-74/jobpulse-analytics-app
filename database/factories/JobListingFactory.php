<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobListingFactory extends Factory
{
    protected $model = JobListing::class;

    public function definition(): array
    {
        return [
            'remotive_id' => $this->faker->unique()->numberBetween(1000, 999999),
            'title' => $this->faker->jobTitle(),
            'company_name' => $this->faker->company(),
            'company_logo_url' => null,
            'category' => 'software-dev',
            'job_type' => 'full_time',
            'candidate_location' => 'Worldwide',
            'salary_text' => null,
            'tags' => ['php', 'laravel'],
            'description' => $this->faker->paragraph(),
            'apply_url' => $this->faker->url(),
            'published_at' => now(),
            'first_seen_at' => now(),
            'is_active' => true,
        ];
    }
}
