<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // NOTA: Lógica de autorização para perfil 'Gestor' ou 'Funcionario'.
        return true;
    }

    public function rules(): array
    {
        $alunoId = $this->route('aluno')->id;

        return [
            'nome' => ['sometimes', 'required', 'string', 'max:255'],
            'cpf' => ['sometimes', 'required', 'string', 'size:11', Rule::unique('alunos')->ignore($alunoId)],
            'data_nascimento' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'turma' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}