<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    private Company $company;

    private Company $company2;

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
    }

    /**
     * A basic feature test example.
     */
    public function test_users_get_endpoint(): void
    {
        $token = $this->getAuthToken(); // Obtém um token automaticamente
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
            ])->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure([
           '*' => ['id', 'name', 'email', 'avatar', 'type', 'created_at', 'updated_at'],
    ]);

    }

    public function test_users_post_endpoint(): void
    {
        // Criar um usuário autenticado
        $user = User::factory()->create(['type' => 'user']);
        $token = JWTAuth::fromUser($user);

        // Criar os dados de um novo usuário (para evitar conflito de e-mail)
        // Criar os dados de um novo usuário, garantindo que a senha seja incluída
        $newUserData = User::factory()->make()->toArray();
        $newUserData['email'] = fake()->unique()->safeEmail(); // Garante um e-mail único
        $newUserData['password'] = 'password123'; // Definir uma senha válida
        $newUserData['password_confirmation'] = 'password123'; // Se a API requer confirmação

        // Enviar a requisição autenticada
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/user', $newUserData);

        // Verificar resposta
        $response->assertStatus(201) // Se a API cria um usuário, deveria retornar 201
            ->assertJsonStructure([
               'id',
               'name',
               'email',
               'type',
               'avatar',
               'created_at',
               'updated_at',
            ]);
    }

    public function test_users_show_endpoint(): void
    {
        // Obtém um token automaticamente
        $token = $this->getAuthToken();
        // Cria um usuário fake
        $user = User::factory()->create();
        // Envia a requisição GET
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson("/api/user/{$user->id}");
        // Verifica se o status da resposta é 200
        $response->assertStatus(200)
            ->assertJsonStructure([ // Verifica a estrutura do JSON
               'data' => ['id',
               'name',
               'email',
               'avatar',
               'type',
               'created_at',
               'updated_at',
            ]]);
    }

    public function test_usuario_autenticado_pode_atualizar_seus_dados(): void
    {
        // Criar usuário com o tipo user
        $user = User::factory()->create(['type' => 'user']);
        // Gerar um token JWT para o user
        $token = JWTAuth::fromUser($user);

        // Novos dados para atualização
        $updatedData = [
            'name' => 'Novo Nome',
            'email' => 'novoteste@email.com',
            'type' => $user->type,
            'avatar' => $user->avatar,

        ];

        // Requisição autenticada
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/user/{$user->id}", $updatedData);

        // Verificações
        $response->assertStatus(200); // Esperamos um status 200 OK
        //  dd($user);
        $this->assertDatabaseHas('users', $updatedData); // Verifica se os dados foram atualizados no banco de dados
    }

    public function test_usuario_tipo_user_nao_pode_atualizar_outro_usuario()
    {
        // Criar dois usuários comuns
        $user1 = User::factory()->create(['type' => 'user']);
        $user2 = User::factory()->create(['type' => 'user']);

        // Gerar um token JWT para o user1
        $token = JWTAuth::fromUser($user1);

        // Dados para atualização
        $updatedData = [
            'name' => 'Nome Indevido',
        ];

        // Tentar atualizar o user2 com o token do user1
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/user/{$user2->id}", $updatedData);

        // Verificamos se a API retorna um erro 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_admin_pode_atualizar_qualquer_usuario()
    {
        // Criar um usuário admin
        $admin = User::factory()->create(['type' => 'admin']);

        // Criar um usuário comum
        $user = User::factory()->create(['type' => 'user']);

        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($admin);

        // Novos dados para atualização
        $updatedData = [
            'name' => 'Nome Atualizado pelo Admin',
        ];

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/user/{$user->id}", $updatedData);

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Nome Atualizado pelo Admin',
                ],
            ]);

        // Verificar se os dados foram realmente alterados no banco
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado pelo Admin',
        ]);
    }

    public function test_somente_user_admin_pode_deletar_usuario()
    {
        // Criar um usuário comum
        $user = User::factory()->create(['type' => 'user']);

        // Gerar um token JWT para o user
        $token = JWTAuth::fromUser($user);

        // Fazer a requisição autenticada com o user
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/user/{$user->id}");

        // Verificar se a API retornou status 403 Forbidden
        $response->assertStatus(403);

        // Criar um usuário admin
        $admin = User::factory()->create(['type' => 'admin']);

        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($admin);

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/user/{$user->id}");

        // Verificar se a API retornou status 204 No Content
        $response->assertStatus(204);

        // Verificar se o usuário foi realmente deletado do banco
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
