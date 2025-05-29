<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lancamento;
use App\Models\Categoria;
use Inertia\Inertia;
use App\Http\Requests\StoreLancamentoRequest;

class LancamentoController extends Controller
{

    public function index(Request $request)
    {   
        $lancamentos = Lancamento::all();

        return Inertia::render('Lancamentos/Index', [
            'lancamentos' => $lancamentos,
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
        $lancamentos = $request->validated()['lancamentos'];

        $lancamentos = array_map(function ($lancamento) {
            $lancamento['created_at'] = now();
            $lancamento['updated_at'] = now();
            return $lancamento;
        }, $lancamentos);
        
        Lancamento::insert($lancamentos);

        return redirect()->route('lancamentos.index');
    }
}
