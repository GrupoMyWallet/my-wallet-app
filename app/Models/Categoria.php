<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'nome', 'tipo',
    ];

    public function lancamentos()
    {
        return $this->hasOne(Lancamento::class);
    }

    public function orcamento()
    {
        return $this->hasOne(Orcamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
