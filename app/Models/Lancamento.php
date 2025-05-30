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
        'user_id', 'tipo', 'valor', 'descricao', 'categoria_id', 'date',
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

        
    public static function rules()
    {
        return [
            'tipo'  => 'required|in:despesa,receita',
            'valor' => 'required|numeric|min:0.01|max:999999999999.99',
            'descricao' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'data' => 'required|date_format:d/m/Y',
            'tipo_recorrencia' => 'required|in:none,mensal,anual,diferente',
            'recorrencia_diferente_meses' => 'required_if:tipo_recorrencia,diferente|nullable|integer|min:1',
            'fim_da_recorrencia' => 'nullable|date|after_or_equal:date',
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

            'date.required' => 'A data é obrigatória.',
            'date.date' => 'Informe uma data válida.',

            'tipo_recorrencia.required' => 'O tipo de recorrência é obrigatório.',
            'tipo_recorrencia.in' => 'Tipo de recorrência inválido.',

            'recorrencia_diferente_meses.integer' => 'A recorrência diferente deve ser um número inteiro.',
            'recorrencia_diferente_meses.min' => 'A recorrência diferente deve ser pelo menos 1 mês.',
            

            'fim_da_recorrencia.date' => 'Informe uma data final de recorrência válida.',
            'fim_da_recorrencia.after_or_equal' => 'A data final da recorrência deve ser igual ou posterior à data inicial.',

            'esta_ativa.boolean' => 'O campo "ativa" deve ser verdadeiro ou falso.',
        ];
    }
}
