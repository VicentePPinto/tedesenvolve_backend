<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test user login failure.
     */
    public function test_user_login_received_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Teste',
            'email' => 'teste@email.com',
            'password' => bcrypt('senha123'),
            'type' => 'admin',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste@email.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    /**
     * Test user login failure.
     */
    public function test_user_login_failure(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401) // Unauthorized
            ->assertJson(['error' => 'Token not valid']);
    }
}
