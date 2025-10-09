<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;
use App\Traits\HandleProprietario;
use Illuminate\Auth\Access\Response;

class CategoriaPolicy
{

    use HandleProprietario;

    protected function isGeralOuPropietario(User $user, Categoria $categoria)
    {
        return $categoria->user_id !== null && $this->isOwner($user, $categoria);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Categoria $categoria): bool
    {   
        if (is_null($categoria->user_id)) {
            return true;
        }

        return $this->isOwner($user, $categoria);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Categoria $categoria): bool
    {
        return $this->isGeralOuPropietario($user, $categoria);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Categoria $categoria): bool
    {   
        return $this->isGeralOuPropietario($user, $categoria);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Categoria $categoria): bool
    {
        return $this->isGeralOuPropietario($user, $categoria);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Categoria $categoria): bool
    {
        return $this->isGeralOuPropietario($user, $categoria);
    }
}
