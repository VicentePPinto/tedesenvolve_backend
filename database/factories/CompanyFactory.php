<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->company,
            'name_fantasy' => $this->faker->company,
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'email' => $this->faker->unique()->safeEmail,
            'logo' => $this->faker->imageUrl(),
            'website' => $this->faker->url,
        ];
    }
}
