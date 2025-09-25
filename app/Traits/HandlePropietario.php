<?php

namespace App\Traits;

use App\Models\User;

trait HandlePropietario
{
    protected function isPropietario(User $user, $model){
        return $user->id === $model->user_id;
    }
}
