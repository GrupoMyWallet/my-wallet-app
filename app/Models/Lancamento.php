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
        'user_id', 'tipo', 'valor', 'descricao', 'categoria_id', 'data',
        'intervalo_meses', 'fim_da_recorrencia', 'esta_ativa',
    ];

    protected $casts = [
        'data' => 'date:d/m/Y',
        'fim_da_recorrencia' => 'date:d/m/Y',
        'valor' => 'decimal:2',
        'esta_ativa' => 'boolean',
    ];

    public function isDespesa()
    {
        return $this->type === 'despesa';
    }

    public function isReceita()
    {
        return $this->type === 'receita';
    }

    public function isRecorrente()
    {
        return $this->recurrence_type !== 'none';
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function rules()
    {
        return [
            'tipo' => 'required|in:despesa,receita',
            'valor' => 'required|numeric|min:0.01|max:999999999999.99',
            'descricao' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'data' => 'required|date_format:d/m/Y',
            'intervalo_meses' => 'required|integer|min:0',
            'fim_da_recorrencia' => 'nullable|date_format:d/m/Y|after_or_equal:data',
            'esta_ativa' => 'boolean',
        ];
    }

    public static function messages()
    {
        return [
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser despesa ou receita.',

            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor mínimo é 0,01.',
            'valor.max' => 'O valor máximo é 999999999999,99.',

            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição deve ter no máximo 255 caracteres.',

            'categoria_id.exists' => 'A categoria selecionada não existe.',

            'data.required' => 'A data é obrigatória.',
            'data.date' => 'A data deve ser uma data válida.',

            'intervalo_meses.required' => 'O intervalo de meses é obrigatório.',
            'intervalo_meses.integer' => 'O intervalo de meses deve ser um número inteiro.',
            'intervalo_meses.min' => 'O intervalo de meses deve ser pelo menos 0.',

            'fim_da_recorrencia.date' => 'A data final da recorrência deve ser uma data válida.',
            'fim_da_recorrencia.after_or_equal' => 'A data final da recorrência deve ser igual ou posterior à data inicial.',

            'esta_ativa.boolean' => 'O campo "ativa" deve ser verdadeiro ou falso.',
        ];
    }
}
