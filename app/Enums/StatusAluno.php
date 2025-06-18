<?php

namespace App\Enums;

enum StatusAluno: string
{
    case PENDENTE = 'Pendente';
    case APROVADO = 'Aprovado';
    case CANCELADO = 'Cancelado';
}