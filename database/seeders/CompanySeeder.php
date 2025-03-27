<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Company::factory()->create([
            'name' => 'Company Test',
            'name_fantasy' => 'Company Test',
            'cnpj' => '00.000.000/0000-00',

        ]);
    }
}
