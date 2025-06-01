<?php 

namespace App\Repositories;

use App\Models\Orcamento;

class OrcamentoRepository
{
    public function getCategoriasDoUsuario($userId)
    {
        return Categoria::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->orWhereNull('user_id');      
        })->get();
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
