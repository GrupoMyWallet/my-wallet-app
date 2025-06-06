<?php 

namespace App\Repositories;

use App\Models\Lancamento;

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

    public function paginateLancamentosDoUsuarioComCategoria($userId, int $paginas = 10)
    {
        return $this->model::with('categoria:id,nome')->where('user_id', $userId)->paginate($paginas);

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
                          ->where(function($query) use ($targetDate, $month, $year) {
                              
                              $query->where(function($q) use ($month, $year) {
                                  $q->where('tipo_recorrencia', 'unica')
                                    ->whereMonth('data', $month)
                                    ->whereYear('data', $year);
                              })
                              
                              ->orWhere(function($q) use ($targetDate) {
                                  $q->whereIn('tipo_recorrencia', ['mensal', 'anual'])
                                    ->where('data', '<=', $targetDate)
                                    ->where(function($subQ) use ($targetDate) {
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
            ->where(function($query) use ($dataInicio, $dataFim) {
                $query->where('data', '<=', $dataFim)
                      ->where(function($q) use ($dataInicio) {
                          $q->whereNull('fim_da_recorrencia')
                            ->orWhere('fim_da_recorrencia', '>=', $dataInicio);
                      });
            })
            ->with('categoria') // Se você tiver relacionamento com categoria
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
}
