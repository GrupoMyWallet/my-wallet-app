<?php 

namespace App\Repositories;

use App\Models\Categoria;

class CategoriaRepository
{   
    protected $model;

    public function __construct(Categoria $categoria)
    {
        $this->model = $categoria;
    }
    
    public function getCategoriasDoUsuario($userId)
    {
        return $this->model::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->orWhereNull('user_id');      
        })->get();
    }

    public function getCategoriasComOrçamento(int $userId)
    {
        return $this->model::with('orcamento')
                        ->where('user_id', $userId)
                        ->orWhereNull('user_id')
                        ->orderBy('nome')
                        ->get();
    }

    public function findOrFail($id)
    {
        return $this->model::findOrFail($id);
    }

    public function getCategoriasDespesas()
    {
        return $this->model
            ->where('tipo', 'despesa')
            ->orderBy('nome')
            ->get();
    }
}
