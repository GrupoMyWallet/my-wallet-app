<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Orcamento;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Repositories\OrcamentoRepository;
use App\Repositories\CategoriaRepository;
use App\Http\Requests\OrcamentoRequest;

class OrcamentoController extends Controller
{
    protected $categoriaRepository;
    protected $orcamentoRepository;

    public function __construct(CategoriaRepository $categoriaRepository, OrcamentoRepository $orcamentoRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
        $this->orcamentoRepository = $orcamentoRepository;
    }

    public function index(Request $request)
    {   

        $userId = $request->user()->id;

        $categorias = $this->categoriaRepository->getCategoriasComOrçamento($userId);
    
        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
        ]);
    }

    public function create(Request $request)
    {
        
        $categoriaId = $request->query('categoria_id');
        $categoria = null;
        $userId = $request->user()->id;

        if ($categoriaId) {
            $categoria = $this->categoriaRepository->findOrFail($categoriaId);
        }

        $categorias = $this->categoriaRepository->getCategoriasComOrçamento($userId);

        return inertia('Orcamentos/Create', [
            'categoria_selecionada' => $categoria,
            'categorias' => $categorias,
        ]);
    }

    public function show(Request $request): Response
    {
        


    }

    public function store(OrcamentoRequest $request)
    {   
        $userId = $request->user()->id;
        $orcamento_validado = $request->validated();
        $orcamento_validado['user_id'] = $userId;

        $orcamento = Orcamento::create($orcamento_validado);
        

        return redirect()->route('categorias.index')->with('success', 'Orçamento cadastrado com sucesso!');
    }
}
