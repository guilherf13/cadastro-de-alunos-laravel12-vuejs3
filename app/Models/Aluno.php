<?php

namespace App\Models;

use App\Enums\StatusAluno;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'data_nascimento',
        'turma',
        'status',
        'telefone',
        'curso',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_nascimento' => 'datetime:Y-m-d',
        'status' => StatusAluno::class, // Usando o Enum para o campo status
    ];
}