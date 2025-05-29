<?php 

namespace App\Repositories;

use App\Models\Categoria;

class CategoriaRepository
{
    public function getCategoriasDoUsuario($userId)
    {
        return Categoria::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereNull('user_id');
        })->get();
    }
}
