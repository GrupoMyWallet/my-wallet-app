<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    public function getDashboardData(int $userId, array $filters = []): array
    {
        $ano = $filters['ano'] ?? now()->year;
        $mes = $filters['mes'] ?? null;
        $categoriaId = $filters['categoria_id'] ?? null;

        return [
            'resumo_anual' => $this->getResumoAnual($userId, $ano, $categoriaId),
            'resumo_mensal' => $mes ? $this->getResumoMensal($userId, $ano, $mes, $categoriaId) : null,
            'grafico_receitas_despesas' => $this->getGraficoReceitasDespesas($userId, $ano, $categoriaId),
            'grafico_orcamento_categoria' => $this->getGraficoOrcamentoCategoria($userId, $ano, $mes, $categoriaId),
            'categorias' => $this->repository->getCategorias($userId),
        ];
    }

    private function getResumoAnual(int $userId, int $ano, ?int $categoriaId = null): array
    {
        $filters = [
            'data_inicio' => Carbon::create($ano)->startOfYear(),
            'data_fim' => Carbon::create($ano)->endOfYear(),
            'categoria_id' => $categoriaId,
        ];

        $lancamentos = $this->repository->getLancamentosComRecorrencia($userId, $filters);

        $receitas = $lancamentos->where('tipo', 'receita')->sum('valor');
        $despesas = $lancamentos->where('tipo', 'despesa')->sum('valor');

        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldo' => $receitas - $despesas,
            'ano' => $ano,
        ];
    }

    private function getResumoMensal(int $userId, int $ano, int $mes, ?int $categoriaId = null): array
    {
        $filters = [
            'data_inicio' => Carbon::create($ano, $mes)->startOfMonth(),
            'data_fim' => Carbon::create($ano, $mes)->endOfMonth(),
            'categoria_id' => $categoriaId,
        ];

        $lancamentos = $this->repository->getLancamentosComRecorrencia($userId, $filters);

        $receitas = $lancamentos->where('tipo', 'receita')->sum('valor');
        $despesas = $lancamentos->where('tipo', 'despesa')->sum('valor');

        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldo' => $receitas - $despesas,
            'mes' => $mes,
            'ano' => $ano,
            'nome_mes' => Carbon::create($ano, $mes)->locale('pt_BR')->monthName,
        ];
    }

    private function getGraficoReceitasDespesas(int $userId, int $ano, ?int $categoriaId = null): array
    {
        $filters = [
            'data_inicio' => Carbon::create($ano)->startOfYear(),
            'data_fim' => Carbon::create($ano)->endOfYear(),
            'categoria_id' => $categoriaId,
        ];

        $lancamentos = $this->repository->getLancamentosComRecorrencia($userId, $filters);

        $dadosPorMes = collect(range(1, 12))->map(function ($mes) use ($lancamentos) {
            $lancamentosMes = $lancamentos->where('mes', $mes);
            
            return [
                'mes' => $mes,
                'nome_mes' => Carbon::create(null, $mes)->locale('pt_BR')->shortMonthName,
                'receitas' => $lancamentosMes->where('tipo', 'receita')->sum('valor'),
                'despesas' => $lancamentosMes->where('tipo', 'despesa')->sum('valor'),
            ];
        });

        return [
            'labels' => $dadosPorMes->pluck('nome_mes')->toArray(),
            'receitas' => $dadosPorMes->pluck('receitas')->toArray(),
            'despesas' => $dadosPorMes->pluck('despesas')->toArray(),
        ];
    }

    private function getGraficoOrcamentoCategoria(int $userId, int $ano, ?int $mes = null, ?int $categoriaId = null): array
    {
        $orcamentos = $this->repository->getOrcamentos($userId, $ano, $mes, $categoriaId);
        
        $filters = [
            'data_inicio' => $mes 
                ? Carbon::create($ano, $mes)->startOfMonth()
                : Carbon::create($ano)->startOfYear(),
            'data_fim' => $mes 
                ? Carbon::create($ano, $mes)->endOfMonth()
                : Carbon::create($ano)->endOfYear(),
            'categoria_id' => $categoriaId,
        ];

        $despesas = $this->repository->getLancamentosComRecorrencia($userId, $filters)
            ->where('tipo', 'despesa')
            ->groupBy('categoria_id');

        $dadosCategoria = collect();

        foreach ($orcamentos as $orcamento) {
            $valorOrcado = $this->calcularValorOrcamento($orcamento, $mes);
            $valorGasto = $despesas->get($orcamento->categoria_id, collect())->sum('valor');

            $dadosCategoria->push([
                'categoria' => $orcamento->categoria->nome,
                'orcado' => $valorOrcado,
                'gasto' => $valorGasto,
                'percentual' => $valorOrcado > 0 ? ($valorGasto / $valorOrcado) * 100 : 0,
            ]);
        }

        return [
            'labels' => $dadosCategoria->pluck('categoria')->toArray(),
            'orcado' => $dadosCategoria->pluck('orcado')->toArray(),
            'gasto' => $dadosCategoria->pluck('gasto')->toArray(),
            'dados' => $dadosCategoria->toArray(),
        ];
    }

    private function calcularValorOrcamento($orcamento, ?int $mes = null): float
    {
        return match ($orcamento->tipo) {
            'anual' => $mes ? $orcamento->valor / 12 : $orcamento->valor,
            'mensal_padrao' => $orcamento->valor,
            'mensal_excecao' => $orcamento->valor,
            default => 0,
        };
    }
}