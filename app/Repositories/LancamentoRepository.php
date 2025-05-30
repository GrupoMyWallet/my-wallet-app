<?php 

namespace App\Repositories;

use App\Models\Lancamento;

class LancamentoRepository
{
    public function getLancamentosDoUsuarioWithCategoria($userId)
    {
        return Lancamento::with('categoria:id,nome')->where('user_id', $userId)->get();
    }

    public function getCategoriasComOrçamento(int $userId)
    {
        return Categoria::with('orcamento')
                        ->where('user_id', $userId)
                        ->orWhereNull('user_id')
                        ->orderBy('nome')
                        ->get();
    }
}
