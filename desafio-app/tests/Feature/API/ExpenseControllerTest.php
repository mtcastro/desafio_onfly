<?php

namespace Tests\Feature\API;

use App\Models\Expense;
use App\Models\User;
use Database\Factories\ExpenseFactory;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index_endpoint()
    {
        // Criar um usuário simulado
        $user = User::factory()->create();

        // Criar algumas despesas para o usuário
        Expense::factory()->count(5)->create([
            'user_id' => $user->id
        ]);

        // Simular a autenticação do usuário
        $this->actingAs($user);

        // Fazer a chamada para o endpoint
        $response = $this->get('/api/expenses');

        // Verificar o status da resposta
        $response->assertStatus(200);

        // Verificar a estrutura do JSON de resposta
        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'description',
                        'date',
                        'user_id',
                        'amount',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);
    }

    public function test_show_endpoint(){

        // Criar um usuário simulado
        $user = User::factory()->create();

        // Criar uma despesa de exemplo
        $expense = Expense::factory()->create([
            'user_id' => $user->id
        ]);

        // Simular a autenticação do usuário
        $this->actingAs($user);

        // Fazer uma requisição GET para a rota de uma despesa específica
        $response = $this->get('/api/expenses/' . $expense->id);

        // Verificar o código de resposta da requisição
        $response->assertStatus(200);

        //dd($response->getContent());

        // Verificar a estrutura do JSON retornado
        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'description',
                'date',
                'user_id',
                'amount',
                'created_at',
                'updated_at',
            ],
        ]);

        // Verificar se os dados da despesa estão presentes na resposta
        $response->assertJson([
            'data' => [
                'id' => $expense->id,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d\TH:i:s.u\Z'), // Converter para string no formato ISO 8601
                'user_id' => $expense->user_id,
                'amount' => ''.$expense->amount,
                'created_at' => $expense->created_at->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $expense->updated_at->format('Y-m-d\TH:i:s.u\Z'),
            ],
        ]);
    }

    public function test_store_endpoint(){

        // Criar um usuário simulado
        $user = User::factory()->create();

        // Criar uma despesa de exemplo
        $expense = Expense::factory()->create([
            'user_id' => $user->id
        ]);

        // Simular a autenticação do usuário
        $this->actingAs($user);

        $data = [
            'description' => 'Test Expense',
            'date' => '2023-05-11',
            'amount' => 99.99,
        ];

        $response = $this->post('/api/expenses', $data);

        $response->assertJson([
            'status' => 'success',
            'data' => [
                'description' => 'Test Expense',
                'date' => '2023-05-11T00:00:00.000000Z',
                'user_id' => $user->id,
                'amount' => 99.99,
            ],
        ]);
    }

    public function test_update_endpoint(){
        // Criar um usuário simulado
        $user = User::factory()->create();

        // Criar uma despesa de exemplo
        $expense = Expense::factory()->create([
            'user_id' => $user->id
        ]);

        // Simular a autenticação do usuário
        $this->actingAs($user);

        $data = [
            'description' => 'Updated Expense',
            'date' => '2023-05-11',
            'amount' => 99.99,
        ];

        $response = $this->put('/api/expenses/'.$expense->id, $data);

        $response->assertJson([
            'status' => 'success',
            'data' => [
                'description' => 'Updated Expense',
                'date' => '2023-05-11T00:00:00.000000Z',
                'user_id' => $user->id,
                'amount' => 99.99,
            ],
        ]);
    }

    public function test_destroy_endpoint(){
        // Criar um usuário simulado
        $user = User::factory()->create();

        // Criar uma despesa de exemplo
        $expense = Expense::factory()->create([
            'user_id' => $user->id
        ]);

        // Simular a autenticação do usuário
        $this->actingAs($user);

        $response = $this->delete('/api/expenses/'.$expense->id);


        $response->assertJson([
            'status' => 'success',
            'data' => 'Expense deleted successfully.',
        ]);

        $this->assertDeleted('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function actingAs($user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', "Bearer {$token}");
        parent::actingAs($user);
        
        return $this;
    }
}
