<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Http\Requests\UpdateLancamentoRequest;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use App\Repositories\LancamentoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LancamentoController extends Controller
{
    protected $categoriaRepository;

    protected $lancamentoRepository;

    public function __construct(CategoriaRepository $categoriaRepository, LancamentoRepository $lancamentoRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
        $this->lancamentoRepository = $lancamentoRepository;
    }

    public function index(Request $request)
    {
        $filtros = $request->only(['categoria_id', 'ano', 'mes']);

        $userId = $request->user()->id;
        $lancamentos = $this->lancamentoRepository->paginateLancamentosDoUsuarioComCategoria($filtros, $userId, 10);

        $categorias = $this->categoriaRepository->getCategoriasDoUsuario($userId);

        return Inertia::render('Lancamentos/Index', [
            'filtros' => $filtros,
            'lancamentos' => $lancamentos,
            'categorias' => $categorias,
        ]);
    }

    public function create(Request $request)
    {

        // $lancamentos = [];

        // if ($request) {
        //     $lancamentos = $request;
        // }

        $categorias = Categoria::select('id', 'nome')->get();
        $user = Auth::user();

        return Inertia::render('Lancamentos/Create', [
            'user' => $user,
            'categorias' => $categorias,
        ]);
    }

    public function store(StoreLancamentoRequest $request)
    {
    $validated = $request->validated();

    // Use relation so mutators (date normalization) run per model.
    $user = $request->user();
    $user->lancamentos()->createMany($validated['lancamentos']);

        return redirect()->route('lancamentos.index')->with('success', 'Lançamentos registrados com sucesso!');
    }

    public function update(UpdateLancamentoRequest $request, $id)
    {
        try {
            $dados = $request->validated(); // Mutators on model will handle date formats

            $this->lancamentoRepository->update($id, $dados);

            return redirect()->route('lancamentos.index')->with('success', 'Lançamento atualizado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {

            return redirect()->route('lancamentos.index')->with('error', 'Não foi possível atualizar o lançamento. Tente novamente mais tarde.');
        }

    }

    public function destroy($id)
    {
        try {

            $this->lancamentoRepository->delete($id);

            return redirect()->route('lancamentos.index')->with('success', 'Lançamento excluído com sucesso!');
        } catch (\Exception $e) {
            // Você pode logar o erro se quiser
            // \Log::error("Erro ao excluir lançamento: " . $e->getMessage());

            return redirect()->route('lancamentos.index')->with('error', 'Não foi possível excluir o lançamento. Tente novamente mais tarde.');
        }
    }
}
