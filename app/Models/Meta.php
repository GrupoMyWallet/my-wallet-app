<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'metas';

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'titulo', 'descricao', 'valor_a_alcancar', 'valor_atual', 'data_final', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
