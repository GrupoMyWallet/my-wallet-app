<?php

namespace App\Traits;

use App\Models\User;

trait HandleProprietario
{
    protected function isOwner(User $user, $model){
        return $user->id === $model->user_id;
    }
}
