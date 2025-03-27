<?php

namespace Database\Factories;

use App\Models\TaskState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskState>
 */
class TaskStateFactory extends Factory
{
    protected $model = TaskState::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state' => $this->faker->word,
            'company_id' => 1,
        ];
    }
}
