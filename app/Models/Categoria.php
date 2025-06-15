<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categoria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias';

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'nome', 'tipo',
    ];

    public function lancamentos(): HasMany
    {
        return $this->hasMany(Lancamento::class);
    }

    public function orcamento(): HasMany
    {
        return $this->hasMany(Orcamento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
