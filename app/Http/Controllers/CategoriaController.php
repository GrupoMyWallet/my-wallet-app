<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoriaController extends Controller
{

    protected $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
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

    }

    public function show(Request $request): Response
    {

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'in:despesa,receita'],
        ]);


        Categoria::create([
            'nome'    => $validated['nome'],
            'tipo'    => $validated['tipo'],
            'user_id' => $request->user()->id,
        ]);


        return redirect()->route('categorias.index');
    }
}
