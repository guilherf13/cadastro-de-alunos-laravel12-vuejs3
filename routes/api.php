<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AlunoController;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| NOTA: A implementação completa de autenticação JWT e dos middlewares de perfil
| não está incluída neste código para manter o foco na API de Alunos,
| mas as rotas estão preparadas para recebê-los.
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('alunos', AlunoController::class)->where(['aluno' => '[0-9]+']);
    Route::patch('alunos/{aluno}/status', [AlunoController::class, 'updateStatus'])->where('aluno', '[0-9]+');
});