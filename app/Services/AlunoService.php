<?php

namespace App\Services;

use App\Enums\StatusAluno;
use App\Models\Aluno;
use App\Models\User;
use App\Notifications\StatusAlteradoNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Exemplo de notificação via log
use Illuminate\Auth\Access\AuthorizationException;

class AlunoService
{
    public function getAll(Request $request): Collection
    {
        $query = Aluno::query();

        if ($request->has('nome')) {
            $query->where('nome', 'like', '%' . $request->input('nome') . '%');
        }
        if ($request->has('cpf')) {
            $query->where('cpf', $request->input('cpf'));
        }

        return $query->get();
    }

    public function create(User $user, array $data): Aluno
    {
        if ($user->perfil !== 'Gestor' && $user->perfil !== 'Funcionario') {
            throw new AuthorizationException('Você não tem permissão para criar alunos.');
        }
        return Aluno::create($data);
    }

    public function update(User $user, Aluno $aluno, array $data): bool
    {
        if ($user->perfil !== 'Gestor' && $user->perfil !== 'Funcionario') {
            throw new AuthorizationException('Você não tem permissão para editar alunos.');
        }
        return $aluno->update($data);
    }

    public function delete(User $user, Aluno $aluno): void
    {
        if ($user->perfil !== 'Gestor') {
            throw new AuthorizationException('Você não tem permissão para deletar alunos.');
        }
        $aluno->delete();
    }

    public function updateStatus(User $user, Aluno $aluno, array $data): Aluno
    {
        if ($user->perfil !== 'Gestor') {
            throw new AuthorizationException('Você não tem permissão para alterar o status de alunos.');
        }

        $novoStatus = StatusAluno::from($data['status']);

        // Regra de negócio: impede a alteração para 'Cancelado' se já estiver 'Aprovado'.
        if ($aluno->status === StatusAluno::APROVADO && $novoStatus === StatusAluno::CANCELADO) {
            throw new \Exception('Não é permitido cancelar o cadastro de um aluno já aprovado.');
        }

        $aluno->status = $novoStatus;
        $aluno->save();

        // Notificação para o gestor
        $this->notificarGestor($aluno);

        return $aluno;
    }

    protected function notificarGestor(Aluno $aluno): void
    {
        // Exemplo de notificação via log, conforme sugerido no teste.
        // Em um projeto real, aqui seria o envio de e-mail, push notification, etc.
        // Poderíamos buscar o usuário gestor e usar a facade Notification::send().
        Log::info("Status do aluno {$aluno->nome} (ID: {$aluno->id}) alterado para {$aluno->status->value}.");
    }
}