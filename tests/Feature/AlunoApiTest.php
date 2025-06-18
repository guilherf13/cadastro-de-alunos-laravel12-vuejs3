<?php

use App\Models\Aluno;
use App\Enums\StatusAluno;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
            // Criar alunos de teste
            $aluno1 = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $aluno2 = Aluno::create([
                'nome' => 'Maria Santos',
                'cpf' => '98765432109',
                'data_nascimento' => '1998-03-20',
                'turma' => 'Turma B',
                'status' => StatusAluno::APROVADO
            ]);

            $response = $this->getJson('/api/alunos');
            
            $response->assertStatus(200)
                    ->assertJsonCount(2, 'data')
                    ->assertJsonStructure([
                        'data' => [
                            '*' => [
                                'id',
                                'nome',
                                'cpf',
                                'data_nascimento',
                                'turma',
                                'status',
                                'created_at',
                                'updated_at'
                            ]
                        ]
                    ]);
        });
    });

    describe('POST /api/alunos - Criação de Aluno', function () {
        
        test('deve criar aluno com dados válidos', function () {
            $dadosAluno = [
                'nome' => 'Ana Costa',
                'cpf' => '55566677788',
                'data_nascimento' => '1992-07-25',
                'turma' => 'Turma C',
                'status' => 'Pendente'
            ];

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(201)
                    ->assertJsonStructure([
                        'data' => [
                            'id',
                            'nome',
                            'cpf',
                            'data_nascimento',
                            'turma',
                            'status',
                            'created_at',
                            'updated_at'
                        ]
                    ])
                    ->assertJson([
                        'data' => [
                            'nome' => 'Ana Costa',
                            'cpf' => '55566677788',
                            'data_nascimento' => '1992-07-25',
                            'turma' => 'Turma C',
                            'status' => 'Pendente'
                        ]
                    ]);

            // Verificar se foi salvo no banco
            $this->assertDatabaseHas('alunos', [
                'nome' => 'Ana Costa',
                'cpf' => '55566677788'
            ]);
        });

        test('deve retornar erro 422 quando CPF já existe', function () {
            // Criar aluno primeiro
            Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            // Tentar criar outro com mesmo CPF
            $dadosAluno = [
                'nome' => 'Outro Nome',
                'cpf' => '12345678901',
                'data_nascimento' => '1990-01-01',
                'turma' => 'Turma B',
                'status' => 'Pendente'
            ];

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['cpf']);
        });

        test('deve retornar erro 422 quando dados obrigatórios estão ausentes', function () {
            $response = $this->postJson('/api/alunos', []);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['nome', 'cpf', 'data_nascimento', 'turma', 'status']);
        });

        test('deve retornar erro 422 quando status é inválido', function () {
            $dadosAluno = [
                'nome' => 'Teste Status',
                'cpf' => '99988877766',
                'data_nascimento' => '1990-01-01',
                'turma' => 'Turma X',
                'status' => 'StatusInvalido'
            ];

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['status']);
        });

        test('deve retornar erro 422 quando CPF tem formato inválido', function () {
            $dadosAluno = [
                'nome' => 'Teste CPF',
                'cpf' => '123', // CPF muito curto
                'data_nascimento' => '1990-01-01',
                'turma' => 'Turma X',
                'status' => 'Pendente'
            ];

            $response = $this->postJson('/api/alunos', $dadosAluno);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['cpf']);
        });
    });

    describe('GET /api/alunos/{id} - Busca por ID', function () {
        
        test('deve retornar aluno quando ID existe', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $response = $this->getJson("/api/alunos/{$aluno->id}");

            $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'id' => $aluno->id,
                            'nome' => 'João Silva',
                            'cpf' => '12345678901',
                            'data_nascimento' => '1995-05-15',
                            'turma' => 'Turma A',
                            'status' => 'Pendente'
                        ]
                    ]);
        });

        test('deve retornar erro 404 quando ID não existe', function () {
            $idInexistente = '00000000-0000-0000-0000-000000000000';
            
            $response = $this->getJson("/api/alunos/{$idInexistente}");

            $response->assertStatus(404);
        });
    });

    describe('PUT /api/alunos/{id} - Atualização Completa', function () {
        
        test('deve atualizar aluno com dados válidos', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $dadosAtualizacao = [
                'nome' => 'João Silva Atualizado',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma B Atualizada',
                'status' => 'Pendente'
            ];

            $response = $this->putJson("/api/alunos/{$aluno->id}", $dadosAtualizacao);

            $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'id' => $aluno->id,
                            'nome' => 'João Silva Atualizado',
                            'turma' => 'Turma B Atualizada'
                        ]
                    ]);

            // Verificar se foi atualizado no banco
            $this->assertDatabaseHas('alunos', [
                'id' => $aluno->id,
                'nome' => 'João Silva Atualizado',
                'turma' => 'Turma B Atualizada'
            ]);
        });

        test('deve retornar erro 422 ao tentar atualizar com CPF de outro aluno', function () {
            // Criar dois alunos
            $aluno1 = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $aluno2 = Aluno::create([
                'nome' => 'Maria Santos',
                'cpf' => '98765432109',
                'data_nascimento' => '1998-03-20',
                'turma' => 'Turma B',
                'status' => StatusAluno::APROVADO
            ]);

            // Tentar atualizar aluno1 com CPF do aluno2
            $dadosAtualizacao = [
                'nome' => 'João Silva',
                'cpf' => '98765432109', // CPF do aluno2
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => 'Pendente'
            ];

            $response = $this->putJson("/api/alunos/{$aluno1->id}", $dadosAtualizacao);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['cpf']);
        });

        test('deve retornar erro 404 para ID inexistente', function () {
            $idInexistente = '00000000-0000-0000-0000-000000000000';
            
            $dadosAtualizacao = [
                'nome' => 'Teste',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => 'Pendente'
            ];

            $response = $this->putJson("/api/alunos/{$idInexistente}", $dadosAtualizacao);

            $response->assertStatus(404);
        });
    });

    describe('PATCH /api/alunos/{id}/status - Atualização de Status', function () {
        
        test('deve atualizar status para Aprovado', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", [
                'status' => 'Aprovado'
            ]);

            $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'id' => $aluno->id,
                            'status' => 'Aprovado'
                        ]
                    ]);

            // Verificar se foi atualizado no banco
            $this->assertDatabaseHas('alunos', [
                'id' => $aluno->id,
                'status' => 'Aprovado'
            ]);
        });

        test('deve atualizar status para Cancelado', function () {
            $aluno = Aluno::create([
                'nome' => 'Maria Santos',
                'cpf' => '98765432109',
                'data_nascimento' => '1998-03-20',
                'turma' => 'Turma B',
                'status' => StatusAluno::PENDENTE
            ]);

            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", [
                'status' => 'Cancelado'
            ]);

            $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'id' => $aluno->id,
                            'status' => 'Cancelado'
                        ]
                    ]);
        });

        test('deve retornar erro 422 para status inválido', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", [
                'status' => 'StatusInvalido'
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['status']);
        });

        test('deve retornar erro 422 ao tentar alterar para Pendente', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::APROVADO
            ]);

            // Com base na atualização que vi, apenas Aprovado e Cancelado são permitidos
            $response = $this->patchJson("/api/alunos/{$aluno->id}/status", [
                'status' => 'Pendente'
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['status']);
        });

        test('deve retornar erro 404 para ID inexistente', function () {
            $idInexistente = '00000000-0000-0000-0000-000000000000';
            
            $response = $this->patchJson("/api/alunos/{$idInexistente}/status", [
                'status' => 'Aprovado'
            ]);

            $response->assertStatus(404);
        });
    });

    describe('DELETE /api/alunos/{id} - Exclusão', function () {
        
        test('deve excluir aluno existente', function () {
            $aluno = Aluno::create([
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'data_nascimento' => '1995-05-15',
                'turma' => 'Turma A',
                'status' => StatusAluno::PENDENTE
            ]);

            $response = $this->deleteJson("/api/alunos/{$aluno->id}");

            $response->assertStatus(204);

            // Verificar se foi removido do banco
            $this->assertDatabaseMissing('alunos', [
                'id' => $aluno->id
            ]);
        });

        test('deve retornar erro 404 para ID inexistente', function () {
            $idInexistente = '00000000-0000-0000-0000-000000000000';
            
            $response = $this->deleteJson("/api/alunos/{$idInexistente}");

            $response->assertStatus(404);
        });
    });

    describe('Testes Integrados - Fluxo Completo', function () {
        
        test('deve executar CRUD completo com sucesso', function () {
            // 1. Criar aluno
            $dadosAluno = [
                'nome' => 'Teste Completo',
                'cpf' => '11122233344',
                'data_nascimento' => '1990-10-10',
                'turma' => 'Turma Teste',
                'status' => 'Pendente'
            ];

            $createResponse = $this->postJson('/api/alunos', $dadosAluno);
            $createResponse->assertStatus(201);
            
            $alunoId = $createResponse->json('data.id');

            // 2. Buscar aluno criado
            $showResponse = $this->getJson("/api/alunos/{$alunoId}");
            $showResponse->assertStatus(200)
                        ->assertJson([
                            'data' => [
                                'nome' => 'Teste Completo',
                                'cpf' => '11122233344'
                            ]
                        ]);

            // 3. Atualizar aluno
            $dadosAtualizacao = [
                'nome' => 'Teste Completo Atualizado',
                'cpf' => '11122233344',
                'data_nascimento' => '1990-10-10',
                'turma' => 'Turma Teste Atualizada',
                'status' => 'Pendente'
            ];

            $updateResponse = $this->putJson("/api/alunos/{$alunoId}", $dadosAtualizacao);
            $updateResponse->assertStatus(200)
                          ->assertJson([
                              'data' => [
                                  'nome' => 'Teste Completo Atualizado',
                                  'turma' => 'Turma Teste Atualizada'
                              ]
                          ]);

            // 4. Atualizar status
            $statusResponse = $this->patchJson("/api/alunos/{$alunoId}/status", [
                'status' => 'Aprovado'
            ]);
            $statusResponse->assertStatus(200)
                          ->assertJson([
                              'data' => [
                                  'status' => 'Aprovado'
                              ]
                          ]);

            // 5. Excluir aluno
            $deleteResponse = $this->deleteJson("/api/alunos/{$alunoId}");
            $deleteResponse->assertStatus(204);

            // 6. Verificar que não existe mais
            $checkResponse = $this->getJson("/api/alunos/{$alunoId}");
            $checkResponse->assertStatus(404);
        });
    });
}); 