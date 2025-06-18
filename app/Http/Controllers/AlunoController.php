<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlunoRequest;
use App\Http\Requests\UpdateAlunoRequest;
use App\Http\Requests\UpdateAlunoStatusRequest;
use App\Http\Resources\AlunoResource;
use App\Models\Aluno;
use App\Services\AlunoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;

class AlunoController extends Controller
{
    protected AlunoService $alunoService;

    public function __construct(AlunoService $alunoService)
    {
        $this->alunoService = $alunoService;
    }

    public function index(Request $request)
    {
        $alunos = $this->alunoService->getAll($request);
        return AlunoResource::collection($alunos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlunoRequest $request)
    {
        try {
            $aluno = $this->alunoService->create($request->user(), $request->validated());
            return (new AlunoResource($aluno))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocorreu um erro interno ao criar o aluno.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Aluno $aluno)
    {
        return new AlunoResource($aluno);
    }

    public function update(UpdateAlunoRequest $request, Aluno $aluno)
    {
        try {
            $this->alunoService->update($request->user(), $aluno, $request->validated());
            return new AlunoResource($aluno->fresh());
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    public function destroy(Request $request, Aluno $aluno)
    {
        try {
            $this->alunoService->delete($request->user(), $aluno);
            return response()->noContent();
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    public function updateStatus(UpdateAlunoStatusRequest $request, Aluno $aluno)
    {
        try {
            $alunoAtualizado = $this->alunoService->updateStatus($request->user(), $aluno, $request->validated());
            return new AlunoResource($alunoAtualizado);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}