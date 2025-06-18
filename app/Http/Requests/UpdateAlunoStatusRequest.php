<?php

namespace App\Http\Requests;

use App\Enums\StatusAluno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAlunoStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                new Enum(StatusAluno::class), 
                Rule::in(['Aprovado', 'Cancelado'])
            ],
        ];
    }
}