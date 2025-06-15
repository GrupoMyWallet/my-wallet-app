<?php

namespace App\Repositories;

use App\Models\Lancamento;
use Carbon\Carbon;

class LancamentoRepository
{
    protected $model;

    public function __construct(Lancamento $lancamento)
    {
        $this->model = $lancamento;
    }

    public function getLancamentosDoUsuarioComCategoria($userId)
    {
        return $this->model::with('categoria:id,nome')->where('user_id', $userId)->get();
    }

    public function paginateLancamentosDoUsuarioComCategoria(array $filtros, $userId, int $paginas = 10)
    {
        $query = $this->model::with('categoria:id,nome')
            ->where('user_id', $userId);

        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (! empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (! empty($filtros['ano'])) {
            $query->whereYear('data', $filtros['ano']);
        }

        if (! empty($filtros['mes'])) {
            $query->whereMonth('data', $filtros['mes']);
        }

        return $query->paginate($paginas);

    }

    public function getLancamentosDoUsuarioPorTipo(int $userId, string $tipo)
    {
        return $this->model->where('user_id', $userId)
            ->where('tipo', $tipo)
            ->where('esta_ativa', true)
            ->get();
    }

    public function getLancamentosValidosPorMes(int $userId, int $month, int $year)
    {
        $targetDate = Carbon::create($year, $month, 1);

        return $this->model->where('user_id', $userId)
            ->where('esta_ativa', true)
            ->where(function ($query) use ($targetDate, $month, $year) {

                $query->where(function ($q) use ($month, $year) {
                    $q->where('tipo_recorrencia', 'unica')
                        ->whereMonth('data', $month)
                        ->whereYear('data', $year);
                })
                    ->orWhere(function ($q) use ($targetDate) {
                        $q->whereIn('tipo_recorrencia', ['mensal', 'anual'])
                            ->where('data', '<=', $targetDate)
                            ->where(function ($subQ) use ($targetDate) {
                                $subQ->whereNull('fim_da_recorrencia')
                                    ->orWhere('fim_da_recorrencia', '>=', $targetDate);
                            });
                    });
            })
            ->get();
    }

    public function getCategoriasComOrçamento(int $userId)
    {
        return Categoria::with('orcamento')
            ->where('user_id', $userId)
            ->orWhereNull('user_id')
            ->orderBy('nome')
            ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function update(int $id, array $data)
    {
        $lancamento = $this->model->findOrFail($id);
        $lancamento->update($data);

        return $lancamento;
    }

    public function delete(int $id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getLancamentosAtivos($userId, $dataInicio, $dataFim)
    {
        return Lancamento::where('user_id', $userId)
            ->where('esta_ativa', true)
            ->where(function ($query) use ($dataInicio, $dataFim) {
                $query->where('data', '<=', $dataFim)
                    ->where(function ($q) use ($dataInicio) {
                        $q->whereNull('fim_da_recorrencia')
                            ->orWhere('fim_da_recorrencia', '>=', $dataInicio);
                    });
            })
            ->with('categoria')
            ->get();
    }

    public function getLancamentosPorPeriodo($userId, $ano, $mes = null)
    {
        if ($mes) {
            $dataInicio = Carbon::create($ano, $mes, 1);
            $dataFim = $dataInicio->copy()->endOfMonth();
        } else {
            $dataInicio = Carbon::create($ano, 1, 1);
            $dataFim = $dataInicio->copy()->endOfYear();
        }

        return $this->getLancamentosAtivos($userId, $dataInicio, $dataFim);
    }

    public function getByPeriodo(Carbon $inicio, Carbon $fim): Collection
    {
        return Lancamento::whereBetween('data', [$inicio, $fim])
            ->orderBy('data', 'desc')
            ->get();
    }

    public function getByCategoria(int $categoriaId, Carbon $inicio, Carbon $fim): Collection
    {
        return Lancamento::where('categoria_id', $categoriaId)
            ->whereBetween('data', [$inicio, $fim])
            ->get();
    }

    public function getReceitasDespesasPorMes(int $ano): Collection
    {
        return Lancamento::selectRaw('
                MONTH(data) as mes,
                tipo,
                SUM(valor) as total
            ')
            ->whereYear('data', $ano)
            ->groupBy('mes', 'tipo')
            ->get()
            ->groupBy('mes')
            ->map(function ($items) {
                return [
                    'receitas' => $items->where('tipo', 'receita')->first()->total ?? 0,
                    'despesas' => $items->where('tipo', 'despesa')->first()->total ?? 0,
                    'saldo' => ($items->where('tipo', 'receita')->first()->total ?? 0) -
                              ($items->where('tipo', 'despesa')->first()->total ?? 0),
                ];
            });
    }

    public function getTotalPorTipo(string $tipo, Carbon $inicio, Carbon $fim): float
    {
        return Lancamento::where('tipo', $tipo)
            ->whereBetween('data', [$inicio, $fim])
            ->sum('valor');
    }

    public function getDespesasPorCategoria(Carbon $inicio, Carbon $fim): Collection
    {
        return Lancamento::select('categoria_id', DB::raw('SUM(valor) as total'))
            ->where('tipo', 'despesa')
            ->whereBetween('data', [$inicio, $fim])
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();
    }

    public function getUltimasTransacoes(int $limite = 10): Collection
    {
        return Lancamento::with('categoria')
            ->orderBy('data', 'desc')
            ->limit($limite)
            ->get();
    }

    public function getAnosDisponiveis(): array
    {
        $anos = Lancamento::selectRaw('DISTINCT YEAR(data) as ano')
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();

        return ! empty($anos) ? $anos : [now()->year];
    }

    public function getLancamentosRecorrentes(Carbon $inicio, Carbon $fim): Collection
    {
        return Lancamento::where('intervalo_meses', '>', 0)
            ->where('data', '<=', $fim)
            ->where(function ($query) use ($fim) {
                $query->whereNull('fim_da_recorrencia')
                    ->orWhere('fim_da_recorrencia', '>=', $fim);
            })
            ->get();
    }
}
