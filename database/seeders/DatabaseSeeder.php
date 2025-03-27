<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(CompanySeeder::class);
        User::factory()->create([
            'name' => 'User Test',
            'email' => 'test@mail.com',
            'password' => 'test@mail.com',
            'type' => 'admin',
            'company_id' => 1,
        ]);
    }
}
