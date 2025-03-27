<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskState;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskControllerTest extends TestCase
{
    use DatabaseMigrations;

    private TaskState $taskState;

    private TaskCategory $taskCategory;

    private User $admin;

    private User $user;

    private User $user2;

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

        // Criar estados e categorias da tarefa
        $this->taskState = TaskState::factory()->create();
        $this->taskCategory = TaskCategory::factory()->create();
        // Criar usuários
        $this->admin = User::factory()->create(['type' => 'admin', 'company_id' => $this->company->id]);
        $this->user = User::factory()->create(['type' => 'user', 'company_id' => $this->company->id]);
        $this->user2 = User::factory()->create(['type' => 'user', 'company_id' => $this->company2->id]);

    }

    public function test_usuario_admin_pode_verificar_todas_tarefas(): void
    {
        // Gerar um token JWT para o usuário
        $token = JWTAuth::fromUser($this->admin);

        // Enviar a requisição autenticada
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/task');

        // Verificar resposta
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'description', 'due_date', 'task_state_id', 'task_category_id', 'user_id', 'company_id', 'created_at', 'updated_at'],
            ]);
    }

    /** Usu[ario pode criar uma tarefa para si mesmo */
    public function test_usuario_pode_criar_task_para_si_mesmo()
    {

        // Gerar um token JWT para o user
        $token = JWTAuth::fromUser($this->user);
        //  dd($this->user);
        $newTaskData = Task::factory()->make()->toArray();
        $newTaskData['task_state_id'] = $this->taskState->id;
        $newTaskData['task_category_id'] = $this->taskCategory->id;
        $newTaskData['user_id'] = $this->user->id;
        $newTaskData['company_id'] = $this->company->id;

        $response = $this->withHeaders([
          'Authorization' => "Bearer $token",
          ])->postJson('/api/task', $newTaskData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', $newTaskData);
    }

    public function test_user_nao_pode_criar_task_para_user()
    {

        // Gerar um token JWT para o user
        $token = JWTAuth::fromUser($this->user);
        $newTaskData = Task::factory()->make()->toArray();
        $newTaskData['task_state_id'] = $this->taskState->id;
        $newTaskData['task_category_id'] = $this->taskCategory->id;
        $newTaskData['user_id'] = $this->user2->id;

        $response = $this->withHeaders([
          'Authorization' => "Bearer $token",
          ])->postJson('/api/task', $newTaskData);

        $response->assertStatus(403);

    }

    public function test_admin_pode_criar_task_para_user()
    {

        // Gerar um token JWT para o user
        $token = JWTAuth::fromUser($this->admin);
        //  dd($this->user);
        $newTaskData = Task::factory()->make()->toArray();
        $newTaskData['task_state_id'] = $this->taskState->id;
        $newTaskData['task_category_id'] = $this->taskCategory->id;
        $newTaskData['user_id'] = $this->user->id;

        $response = $this->withHeaders([
          'Authorization' => "Bearer $token",
          ])->postJson('/api/task', $newTaskData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', $newTaskData);

    }

    public function test_user_pode_atualizar_suas_tarefas()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->user);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Novos dados para atualização
        $task->title = 'Tarefa Update';

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/task/{$task->id}", $task->toArray());

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Tarefa Update',
                ],
            ]);
    }

    public function test_user_nao_pode_atualizar_tarefas_de_outro_user()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->user2);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Novos dados para atualização
        $task->title = 'Tarefa Update';

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/task/{$task->id}", $task->toArray());

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(403);
    }

    public function test_admin_pode_atualizar_tarefas_de_user()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->admin);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Novos dados para atualização
        $task->title = 'Tarefa Update';

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/task/{$task->id}", $task->toArray());

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Tarefa Update',
                ],
            ]);
    }

    public function test_usuario_pode_excluir_suas_tarefas()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->user);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/task/{$task->id}");

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(204);
    }

    public function test_usuario_nao_pode_excluir_tarefas_de_outro_user()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->user2);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/task/{$task->id}");

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(403);
    }

    public function test_admin_pode_excluir_tarefas_de_user()
    {
        // Gerar um token JWT para o admin
        $token = JWTAuth::fromUser($this->admin);
        // Criar um usuário admin
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Fazer a requisição autenticada com o admin
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson("/api/task/{$task->id}");

        // Verificar se a API permitiu e retornou status 200
        $response->assertStatus(204);
    }

    public function test_usuario_pode_verificar_suas_tarefas_em_periodo()
    {
        // Gerar um token JWT para o usuário
        $token = JWTAuth::fromUser($this->user);

        $task = Task::factory(10)->create(['user_id' => $this->user->id, 'due_date' => now()->subDays(5), 'created_at' => now()->subDays(10)]);

        $newTaskData = [
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'user_id' => $this->user->id,
        ];
        // dd($newTaskData);
        // Enviar a requisição autenticada
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/getTasksInPeriod', $newTaskData);
        // dd($response);
        // Verificar resposta
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(10), // Ou use um intervalo adequado
        ]);
    }

    public function test_usario_nao_pode_acessar_task_outra_company()
    {
        // Gerar um token JWT para o usuário
        $token = JWTAuth::fromUser($this->user);

        $task = Task::factory(10)->create(['user_id' => $this->user->id, 'due_date' => now()->subDays(5), 'created_at' => now()->subDays(10)]);

        $newTaskData = [
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'user_id' => $this->user2->id,
        ];
        // dd($newTaskData);
        // Enviar a requisição autenticada
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/getTasksInPeriod', $newTaskData);
        // dd($response);
        // Verificar resposta
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
