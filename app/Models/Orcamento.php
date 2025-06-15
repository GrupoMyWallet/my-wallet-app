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
        'user_id', 'tipo', 'categoria_id', 'ano', 'mes', 'valor',
    ];

    protected $casts = [
        'valor' => 'decimal:2', 'ano' => 'integer', 'mes' => 'integer',
    ];

    protected $appends = [
        'mes_formatado',
        'periodo_formatado',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMesFormatadoAttribute(): ?string
    {
        if (! $this->mes) {
            return null;
        }

        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];

        return $meses[$this->mes] ?? null;
    }

    public function getPeriodoFormatadoAttribute(): string
    {
        if ($this->tipo === 'anual') {
            return (string) $this->ano;
        }

        return $this->mes_formatado.'/'.$this->ano;
    }

    public function scopeByCategoria($query, $categoriaId)
    {
        if ($categoriaId) {
            return $query->where('categoria_id', $categoriaId);
        }

        return $query;
    }

    public function scopeByTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo', $tipo);
        }

        return $query;
    }

    public function scopeByAno($query, $ano)
    {
        if ($ano) {
            return $query->where('ano', $ano);
        }

        return $query;
    }

    public function scopeByMes($query, $mes)
    {
        if ($mes) {
            return $query->where('mes', $mes);
        }

        return $query;
    }
}
