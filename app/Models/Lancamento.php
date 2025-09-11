<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Lancamento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lancamentos';

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'tipo', 'valor', 'descricao', 'categoria_id', 'data',
        'intervalo_meses', 'fim_da_recorrencia', 'esta_ativa',
    ];

    protected $casts = [
        'data' => 'date:d/m/Y',
        'fim_da_recorrencia' => 'date:d/m/Y',
        'valor' => 'decimal:2',
        'esta_ativa' => 'boolean',
    'intervalo_meses' => 'integer',
    ];

    /*
     * Mutators to accept both d/m/Y and Y-m-d (or DateTime) on assignment while
     * keeping simple date formatting via casts when reading.
     */
    public function setDataAttribute($value): void
    {
        if ($value instanceof \DateTimeInterface) {
            $this->attributes['data'] = $value;
            return;
        }

        if (is_string($value) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            $this->attributes['data'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            return;
        }

        $this->attributes['data'] = $value; // Assume already in correct format
    }

    public function setFimDaRecorrenciaAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['fim_da_recorrencia'] = null;
            return;
        }

        if ($value instanceof \DateTimeInterface) {
            $this->attributes['fim_da_recorrencia'] = $value;
            return;
        }

        if (is_string($value) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            $this->attributes['fim_da_recorrencia'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            return;
        }

        $this->attributes['fim_da_recorrencia'] = $value;
    }

    public function isDespesa()
    {
        return $this->tipo === 'despesa';
    }

    public function isReceita()
    {
        return $this->tipo === 'receita';
    }

    public function isRecorrente()
    {
        return $this->intervalo_meses !== 0;
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
     * Query scopes
     */
    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAno($query, int $ano)
    {
        return $query->whereYear('data', $ano);
    }

    public function scopeMes($query, int $mes)
    {
        return $query->whereMonth('data', $mes);
    }

    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
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
