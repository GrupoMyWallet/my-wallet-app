<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
            /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lancamentos';

    protected $fillable = [
        'user_id', 'tipo', 'valor', 'descricao', 'categoria', 'data',
        'tipo_recorrencia', 'recorrencia_diferente_meses', 'fim_da_recorrencia', 'esta_ativa'
    ];

    public function isDespesa() { return $this->type === 'despesa'; }
    public function isReceita() { return $this->type === 'receita'; }
    public function isRecorrente() { return $this->recurrence_type !== 'none'; }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
