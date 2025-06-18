<?php

namespace App\Http\Requests;

use App\Enums\StatusAluno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // NOTA: Aqui entraria a lógica de autorização baseada em perfil.
        // Ex: return $this->user()->perfil === 'Gestor' || $this->user()->perfil === 'Funcionario';
        return true; // Permitindo para fins de teste
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'unique:alunos,cpf', 'size:11'], // Ajuste o size se necessário
            'data_nascimento' => ['required', 'date_format:Y-m-d'],
            'turma' => ['required', 'string', 'max:255'],
            'status' => ['required', new Enum(StatusAluno::class)],
        ];
    }
}