<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    private Company $company;

    private Company $company2;

    private User $user;

    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarDadosIniciais();
    }

    private function criarDadosIniciais(): void
    {
        // Criar a companhia
        $this->company = Company::factory()->create();
        $this->company2 = Company::factory()->create();

        $this->user = User::factory()->create(['type' => 'user', 'email' => 'teste@email.com',
            'password' => bcrypt('senha123'), 'company_id' => $this->company->id]);
        $this->user2 = User::factory()->create(['type' => 'user', 'email' => 'teste2@email.com',
            'password' => bcrypt('senha123'), 'company_id' => $this->company2->id]);

    }

    /**
     * Test user login failure.
     */
    public function test_user_login_received_token(): void
    {

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
    public function test_userio_sem_token(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(403) // Unauthorized
            ->assertJson(['error' => 'Token not valid']);
    }

    /**
     * Test user login failure.
     */
    public function test_usuario_token_outra_company(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user2->email,
            'password' => 'senha123',
            'company_id' => $this->company->id,
        ]);
        $response->assertStatus(401) // Forbidden
            ->assertJson(['error' => 'Unauthorized']);
    }
}
