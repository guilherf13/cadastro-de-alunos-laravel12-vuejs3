<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlunoRequest;
use App\Http\Requests\UpdateAlunoRequest;
use App\Http\Requests\UpdateAlunoStatusRequest;
use App\Http\Resources\AlunoResource;
use App\Models\Aluno;
use App\Services\AlunoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AlunoController extends Controller
{
    public function __construct(protected AlunoService $alunoService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $alunos = $this->alunoService->getAll($request);
        return AlunoResource::collection($alunos);
    }

    public function store(StoreAlunoRequest $request): AlunoResource
    {
        $aluno = $this->alunoService->create($request->user(), $request->validated());
        return AlunoResource::make($aluno);
    }

    public function show(Aluno $aluno): AlunoResource
    {
        return AlunoResource::make($aluno);
    }

    public function update(UpdateAlunoRequest $request, Aluno $aluno): AlunoResource
    {
        $this->alunoService->update($request->user(), $aluno, $request->validated());
        return AlunoResource::make($aluno->fresh());
    }

    public function destroy(Request $request, Aluno $aluno): Response
    {
        $this->alunoService->delete($request->user(), $aluno);
        return response()->noContent();
    }

    public function updateStatus(UpdateAlunoStatusRequest $request, Aluno $aluno): AlunoResource
    {
        $alunoAtualizado = $this->alunoService->updateStatus($request->user(), $aluno, $request->validated());
        return new AlunoResource($alunoAtualizado);
    }
}