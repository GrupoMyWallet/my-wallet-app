<?php

namespace App\Repositories;

use App\Models\Orcamento;
use Illuminate\Pagination\LengthAwarePaginator;

class OrcamentoRepository
{
    public function __construct(
        private Orcamento $model
    ) {}

    public function paginate(array $filtros = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->with('categoria')
            ->byCategoria($filtros['categoria_id'] ?? null)
            ->byTipo($filtros['tipo'] ?? null)
            ->byAno($filtros['ano'] ?? null)
            ->byMes($filtros['mes'] ?? null)
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Orcamento
    {

        if ($data['tipo'] === 'anual') {
            $data['mes'] = null;
        }

        return $this->model->create($data);
    }

    public function update(Orcamento $orcamento, array $data): bool
    {
        // Se for orçamento anual, garantir que mes seja null
        if ($data['tipo'] === 'anual') {
            $data['mes'] = null;
        }

        return $orcamento->update($data);
    }

    public function delete(Orcamento $orcamento): bool
    {
        return $orcamento->delete();
    }

    public function findById(int $id): ?Orcamento
    {
        return $this->model->with('categoria')->find($id);
    }

    public function existeOrcamento(int $categoriaId, string $tipo, int $ano, ?int $mes = null): bool
    {
        $query = $this->model
            ->where('categoria_id', $categoriaId)
            ->where('tipo', $tipo)
            ->where('ano', $ano);

        if ($tipo === 'mensal') {
            $query->where('mes', $mes);
        } else {
            $query->whereNull('mes');
        }

        return $query->exists();
    }

    public function getOrcamentosPorCategoria(int $categoriaId, int $ano): array
    {
        return $this->model
            ->where('categoria_id', $categoriaId)
            ->where('ano', $ano)
            ->get()
            ->groupBy('tipo')
            ->toArray();
    }
}
