<?php

namespace App\Repositories;

use App\Models\Meta;

class MetaRepository
{
    protected $model;

    public function __construct(Meta $meta)
    {
        $this->model = $meta;
    }

    public function getCategoriaDoUsuario($userId)
    {
        return $this->model::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })->get();
    }

    public function findOrFail($id)
    {
        return $this->model::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $categoria = $this->model->findOrFail($id);
        $categoria->update($data);

        return $categoria;
    }

    public function delete(int $id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getCategoriaDespesas()
    {
        return $this->model
            ->where('tipo', 'despesa')
            ->orderBy('nome')
            ->get();
    }
}
