<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'metas';

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'user_id', 'titulo', 'descricao', 'valor_a_alcancar', 'valor_atual', 'data_final', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
