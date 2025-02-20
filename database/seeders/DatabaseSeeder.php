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

        User::factory()->create([
            'name' => 'Vicente Pinto',
            'email' => 'vicente.si@gmail.com',
            'password' => 'vicente.si@gmail.com',
            'type' => 'admin',
        ]);
    }
}
