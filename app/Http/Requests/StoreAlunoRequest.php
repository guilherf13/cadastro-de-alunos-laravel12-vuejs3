<?php

namespace App\Http\Requests;

use App\Enums\StatusAluno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAlunoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:alunos,email'],
            'cpf' => ['required', 'string', 'unique:alunos,cpf', 'size:11'],
            'data_nascimento' => ['required', 'date_format:Y-m-d'],
            'turma' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'curso' => ['required', 'string', 'max:255'],
            'status' => ['required', new Enum(StatusAluno::class)],
        ];
    }
}