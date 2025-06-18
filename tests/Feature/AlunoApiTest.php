<?php

use App\Models\Aluno;
use App\Models\User;
use App\Enums\StatusAluno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// Configuração inicial para todos os testes neste arquivo
beforeEach(function () {
    // Cria um usuário com perfil de Gestor para ser usado nos testes
    $this->gestor = User::factory()->create(['perfil' => 'Gestor']);
    // Autentica o usuário para as requisições da API
    Sanctum::actingAs($this->gestor);
});

describe('API de Alunos - CRUD Completo', function () {

    describe('GET /api/alunos - Listagem de Alunos', function () {
        
        test('deve retornar lista vazia quando não há alunos', function () {
            $response = $this->getJson('/api/alunos');
            
            $response->assertStatus(200)
                    ->assertJson([
                        'data' => []
                    ]);
        });

        test('deve retornar lista de alunos quando existem alunos no banco', function () {
            // Criar alunos de teste usando factory
            Aluno::factory()->count(2)->create();

            $response = $this->getJson('/api/alunos');
            
            $response->assertStatus(200)
                    ->assertJsonCount(2, 'data')
                    ->assertJsonStructure([
                        'data' => [
                            '*' => [
                                'id', 'nome', 'email', 'cpf', 'data_nascimento', 'turma', 'status'
                            ]
                        ]
                    ]);
        });
    });

    describe('POST /api/alunos - Criação de Aluno', function () {
        
        test('deve criar aluno com dados válidos', function () {
            $dadosAluno = Aluno::factory()->raw();

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(201)
                    ->assertJsonStructure(['data' => ['id', 'nome', 'cpf']])
                    ->assertJson(['data' => ['nome' => $dadosAluno['nome']]]);

            // Verificar se foi salvo no banco
            $this->assertDatabaseHas('alunos', [
                'cpf' => $dadosAluno['cpf']
            ]);
        });

        test('deve retornar erro 422 quando CPF já existe', function () {
            $alunoExistente = Aluno::factory()->create();
            $dadosAluno = Aluno::factory()->raw(['cpf' => $alunoExistente->cpf]);

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['cpf']);
        });

        test('deve retornar erro 422 quando dados obrigatórios estão ausentes', function () {
            $response = $this->postJson('/api/alunos', []);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['nome', 'email', 'cpf', 'data_nascimento', 'turma', 'status']);
        });

        test('deve retornar erro 422 quando status é inválido', function () {
            $dadosAluno = Aluno::factory()->raw(['status' => 'StatusInvalido']);

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['status']);
        });

        test('deve retornar erro 422 quando CPF tem formato inválido', function () {
            $dadosAluno = Aluno::factory()->raw(['cpf' => '123']);
            $response = $this->postJson('/api/alunos', $dadosAluno);
            $response->assertStatus(422)->assertJsonValidationErrors(['cpf']);
        });
    });

    describe('GET /api/alunos/{id} - Busca por ID', function () {
        
        test('deve retornar aluno quando ID existe', function () {
            $aluno = Aluno::factory()->create();

            $response = $this->getJson("/api/alunos/{$aluno->id}");

            $response->assertStatus(200)
                    ->assertJson(['data' => ['id' => $aluno->id]]);
        });

        test('deve retornar erro 404 quando ID não existe', function () {
            $idInexistente = 99999;
            $response = $this->getJson("/api/alunos/{$idInexistente}");
            $response->assertStatus(404);
        });
    });

    describe('PUT /api/alunos/{id} - Atualização Completa', function () {
        
        test('deve atualizar aluno com dados válidos', function () {
            $aluno = Aluno::factory()->create();
            $dadosAtualizacao = Aluno::factory()->raw();
            
            $response = $this->putJson("/api/alunos/{$aluno->id}", $dadosAtualizacao);

            $response->assertStatus(200)
                    ->assertJson(['data' => ['nome' => $dadosAtualizacao['nome']]]);

            $this->assertDatabaseHas('alunos', ['id' => $aluno->id, 'nome' => $dadosAtualizacao['nome']]);
        });

        test('deve retornar erro 422 ao tentar atualizar com CPF de outro aluno', function () {
            $aluno1 = Aluno::factory()->create();
            $aluno2 = Aluno::factory()->create();
            $dadosAtualizacao = ['cpf' => $aluno2->cpf];

            $response = $this->putJson("/api/alunos/{$aluno1->id}", $dadosAtualizacao);
            $response->assertStatus(422)->assertJsonValidationErrors(['cpf']);
        });

        test('deve retornar erro 404 para ID inexistente', function() {
            $dadosAtualizacao = Aluno::factory()->raw();
            $response = $this->putJson('/api/alunos/99999', $dadosAtualizacao);
            $response->assertStatus(404);
        });
    });

    describe('PATCH /api/alunos/{id}/status - Atualização de Status', function () {

        test('deve atualizar status para Aprovado', function () {
            $aluno = Aluno::factory()->create(['status' => StatusAluno::PENDENTE]);

            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", ['status' => 'Aprovado']);
            
            $response->assertStatus(200)->assertJson(['data' => ['status' => 'Aprovado']]);
            $this->assertDatabaseHas('alunos', ['id' => $aluno->id, 'status' => StatusAluno::APROVADO]);
        });

        test('deve atualizar status para Cancelado', function () {
            $aluno = Aluno::factory()->create(['status' => StatusAluno::PENDENTE]);

            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", ['status' => 'Cancelado']);
            
            $response->assertStatus(200)->assertJson(['data' => ['status' => 'Cancelado']]);
            $this->assertDatabaseHas('alunos', ['id' => $aluno->id, 'status' => StatusAluno::CANCELADO]);
        });

        test('deve retornar erro 422 para status inválido', function () {
            $aluno = Aluno::factory()->create();
            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", ['status' => 'Invalido']);
            $response->assertStatus(422)->assertJsonValidationErrors(['status']);
        });

        test('deve retornar erro 422 ao tentar alterar para Pendente', function () {
            $aluno = Aluno::factory()->create(['status' => StatusAluno::APROVADO]);
            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", ['status' => 'Pendente']);
            $response->assertStatus(422); 
        });

        test('deve retornar erro 404 para ID inexistente', function() {
            $response = $this->patchJson('/api/alunos/99999/status', ['status' => 'Aprovado']);
            $response->assertStatus(404);
        });
    });

    describe('DELETE /api/alunos/{id} - Exclusão', function () {

        test('deve excluir aluno existente', function () {
            $aluno = Aluno::factory()->create();
            
            $response = $this->deleteJson("/api/alunos/{$aluno->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('alunos', ['id' => $aluno->id]);
        });

        test('deve retornar erro 404 para ID inexistente', function() {
            $response = $this->deleteJson('/api/alunos/99999');
            $response->assertStatus(404);
        });
    });

    describe('Testes Integrados - Fluxo Completo', function () {
        test('deve executar CRUD completo com sucesso', function () {
            // 1. Criar
            $dadosAluno = Aluno::factory()->raw(['status' => 'Pendente']);
            $createResponse = $this->postJson('/api/alunos', $dadosAluno);
            $createResponse->assertStatus(201);
            
            $alunoId = $createResponse->json('data.id');

            // 2. Buscar
            $this->getJson("/api/alunos/{$alunoId}")->assertStatus(200);

            // 3. Atualizar Status
            $this->patchJson("/api/alunos/{$alunoId}/status", ['status' => 'Aprovado'])
                 ->assertStatus(200)
                 ->assertJson(['data' => ['status' => 'Aprovado']]);

            // 4. Atualizar Dados
            $dadosUpdate = ['nome' => 'Nome Final Teste', 'email' => 'final@teste.com'];
            $this->putJson("/api/alunos/{$alunoId}", $dadosUpdate)
                 ->assertStatus(200)
                 ->assertJson(['data' => ['nome' => 'Nome Final Teste']]);

            // 5. Excluir
            $this->deleteJson("/api/alunos/{$alunoId}")->assertStatus(204);
            $this->assertDatabaseMissing('alunos', ['id' => $alunoId]);
        });
    });

}); 