<?php

namespace Database\Factories;

use App\Models\LeadStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadStatus>
 */
class LeadStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */  protected $model = LeadStatus::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['New Lead', 'In Progress', 'Completed', 'Deal Lost', 'Deal Won']),
        ];
    }
}
