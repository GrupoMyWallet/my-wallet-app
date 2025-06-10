<?php

namespace App\Repositories;

use App\Models\Lancamento;
use App\Models\Categoria;
use App\Models\Orcamento;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class DashboardRepository
{
    public function getLancamentosComRecorrencia(int $userId, array $filters = []): Collection
    {
        $dataInicio = Carbon::parse($filters['data_inicio'] ?? now()->startOfYear());
        $dataFim = Carbon::parse($filters['data_fim'] ?? now()->endOfYear());

        $lancamentos = Lancamento::where('user_id', $userId)
            ->where('esta_ativa', true)
            ->with('categoria')
            ->when($filters['categoria_id'] ?? null, fn($q, $categoriaId) => $q->where('categoria_id', $categoriaId))
            ->get();

        return $this->expandirLancamentosRecorrentes($lancamentos, $dataInicio, $dataFim);
    }

    public function getOrcamentos(int $userId, int $ano, ?int $mes = null, ?int $categoriaId = null): Collection
    {
        return Orcamento::where('user_id', $userId)
            ->where('ano', $ano)
            ->when($mes, function ($query, $mes) {
                return $query->where(function ($q) use ($mes) {
                    $q->where('tipo', 'anual')
                      ->orWhere('tipo', 'mensal_padrao')
                      ->orWhere(function ($subQ) use ($mes) {
                          $subQ->where('tipo', 'mensal_excecao')->where('mes', $mes);
                      });
                });
            })
            ->when($categoriaId, fn($q, $id) => $q->where('categoria_id', $id))
            ->with('categoria')
            ->get();
    }

    public function getCategorias(int $userId, ?string $tipo = null): Collection
    {
        return Categoria::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhereNull('user_id');
            })
            ->when($tipo, fn($q, $tipo) => $q->where('tipo', $tipo))
            ->orderBy('nome')
            ->get();
    }

    private function expandirLancamentosRecorrentes(Collection $lancamentos, Carbon $dataInicio, Carbon $dataFim): Collection
    {
        $lancamentosExpandidos = collect();

        foreach ($lancamentos as $lancamento) {
            $dataLancamento = Carbon::parse($lancamento->data);
            
            // Lançamento único
            if ($lancamento->intervalo_meses == 0) {
                if ($dataLancamento->between($dataInicio, $dataFim)) {
                    $lancamentosExpandidos->push($this->criarLancamentoExpandido($lancamento, $dataLancamento));
                }
                continue;
            }

            // Lançamentos recorrentes
            $fimRecorrencia = $lancamento->fim_da_recorrencia 
                ? Carbon::parse($lancamento->fim_da_recorrencia) 
                : $dataFim;

            $dataAtual = $dataLancamento->copy();
            
            while ($dataAtual <= $fimRecorrencia && $dataAtual <= $dataFim) {
                if ($dataAtual >= $dataInicio) {
                    $lancamentosExpandidos->push($this->criarLancamentoExpandido($lancamento, $dataAtual->copy()));
                }
                $dataAtual->addMonths($lancamento->intervalo_meses);
            }
        }

        return $lancamentosExpandidos;
    }

    private function criarLancamentoExpandido(Lancamento $lancamento, Carbon $data): array
    {
        return [
            'id' => $lancamento->id,
            'tipo' => $lancamento->tipo,
            'valor' => $lancamento->valor,
            'descricao' => $lancamento->descricao,
            'categoria_id' => $lancamento->categoria_id,
            'categoria' => $lancamento->categoria,
            'data' => $data->format('Y-m-d'),
            'mes' => $data->month,
            'ano' => $data->year,
        ];
    }
}