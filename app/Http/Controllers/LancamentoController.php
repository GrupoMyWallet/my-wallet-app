<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Http\Requests\UpdateLancamentoRequest;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use App\Repositories\LancamentoRepository;
use Carbon\Carbon;
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

        $lancamentos = [];

        if ($request) {
            $lancamentos = $request;
        }

        $categorias = Categoria::select('id', 'nome')->get();
        $user = Auth::user();

        return Inertia::render('Lancamentos/Create', [
            'user' => $user,
            'categorias' => $categorias,
            'lancamentos' => $lancamentos,
        ]);
    }

    public function show(Request $request): Response
    {
        return Inertia::render('Lancamentos/Show', [
            'sessions' => $this->sessions($request)->all(),
        ]);
    }

    public function store(StoreLancamentoRequest $request)
    {

        $lancamentos_validados = $request->validated();

        $lancamentos = array_map(function ($lancamento) {
            $lancamento['data'] = Carbon::createFromFormat('d/m/Y', $lancamento['data'])->format('Y-m-d');
            $lancamento['user_id'] = auth()->id();
            $lancamento['created_at'] = now();
            $lancamento['updated_at'] = now();

            return $lancamento;
        }, $lancamentos_validados['lancamentos']);

        $this->lancamentoRepository->insert($lancamentos);

        return redirect()->route('lancamentos.index')->with('success', 'Lançamentos registrados com sucesso!');
    }

    public function update(UpdateLancamentoRequest $request, $id)
    {
        try {
            $lancamento_validado = $request->validated();
            $lancamento_validado['data'] = Carbon::createFromFormat('d/m/Y', $lancamento_validado['data'])->format('Y-m-d');

            $this->lancamentoRepository->update($id, $lancamento_validado);

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
