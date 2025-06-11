<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrcamentoRequest;
use App\Models\Categoria;
use App\Repositories\OrcamentoRepository;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrcamentoController extends Controller
{
    public function __construct(
        private OrcamentoRepository $orcamentoRepository,
        private CategoriaRepository $categoriaRepository
    ) {}

    public function index(Request $request)
    {
        $filtros = $request->only(['categoria_id', 'tipo', 'ano', 'mes']);
        
        $orcamentos = $this->orcamentoRepository->paginate($filtros);
        $categorias = $this->categoriaRepository->getCategoriasDespesas();
        
        $categoriaSelecionada = null;
        if ($request->categoria_id) {
            $categoriaSelecionada = $this->categoriaRepository->findOrFail($request->categoria_id);
        }

        return Inertia::render('Orcamentos/Index', [
            'orcamentos' => $orcamentos,
            'categorias' => $categorias,
            'filtros' => $filtros,
            'categoria_selecionada' => $categoriaSelecionada
        ]);
    }

    public function store(OrcamentoRequest $request)
    {
        $data = $request->validated();
        
        
        if ($this->orcamentoRepository->existeOrcamento(
            $data['categoria_id'],
            $data['tipo'],
            $data['ano'],
            $data['mes'] ?? null
        )) {
            return back()->withErrors([
                'geral' => 'Já existe um orçamento para esta categoria e período.'
            ]);
        }

        $data['user_id'] = auth()->id();

        $this->orcamentoRepository->create($data);

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento criado com sucesso!');
    }

    public function update(OrcamentoRequest $request, int $id)
    {
        $orcamento = $this->orcamentoRepository->findById($id);
        
        if (!$orcamento) {
            return back()->withErrors(['geral' => 'Orçamento não encontrado.']);
        }

        $data = $request->validated();
        
        // Verificar se já existe outro orçamento para esta categoria/tipo/período
        if ($this->orcamentoRepository->existeOrcamento(
            $data['categoria_id'],
            $data['tipo'],
            $data['ano'],
            $data['mes'] ?? null
        ) && (
            $orcamento->categoria_id != $data['categoria_id'] ||
            $orcamento->tipo != $data['tipo'] ||
            $orcamento->ano != $data['ano'] ||
            $orcamento->mes != ($data['mes'] ?? null)
        )) {
            return back()->withErrors([
                'geral' => 'Já existe um orçamento para esta categoria e período.'
            ]);
        }

        $this->orcamentoRepository->update($orcamento, $data);

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $orcamento = $this->orcamentoRepository->findById($id);
        
        if (!$orcamento) {
            return back()->withErrors(['geral' => 'Orçamento não encontrado.']);
        }

        $this->orcamentoRepository->delete($orcamento);

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento excluído com sucesso!');
    }
}