<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lancamento;
use App\Models\Categoria;
use Inertia\Inertia;
use App\Http\Requests\StoreLancamentoRequest;
use App\Http\Requests\UpdateLancamentoRequest;
use App\Repositories\LancamentoRepository;
use App\Repositories\CategoriaRepository;

class LancamentoController extends Controller
{
    public function __construct(CategoriaRepository $categoriaRepository, LancamentoRepository $lancamentoRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
        $this->lancamentoRepository = $lancamentoRepository;
    }

    public function index(Request $request)
    {   
        $userId = $request->user()->id;
        $lancamentos = $this->lancamentoRepository->getLancamentosDoUsuarioWithCategoria($userId);
        $categorias = $this->categoriaRepository->getCategoriasDoUsuario($userId);

        return Inertia::render('Lancamentos/Index', [
            'lancamentos' => $lancamentos,
            'categorias' => $categorias
        ]);
    }

    public function create(Request $request)
    {   

        $lancamentos = [];

        if($request){
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
            $lancamento['user_id'] = auth()->id();
            $lancamento['created_at'] = now();
            $lancamento['updated_at'] = now();
            return $lancamento;
        }, $lancamentos_validados['lancamentos']);
        
        Lancamento::insert($lancamentos);

        return redirect()->route('lancamentos.index');
    }

    public function update(UpdateLancamentoRequest $request, $id)
    {   
        
        $lancamento_validado = $request->validated();

        $lancamento = Lancamento::findOrFail($id);

        $lancamento->update($lancamento_validado);
    
        return redirect()->route('lancamentos.index');
    }
}
