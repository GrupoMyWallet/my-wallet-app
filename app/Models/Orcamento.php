<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orcamentos';

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'user_id', 'categoria_id', 'ano', 'mes', 'valor'
    ];

    public function categorias()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
