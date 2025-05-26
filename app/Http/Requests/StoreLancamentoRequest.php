<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLancamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'tipo' => ['required', Rule::in(['despesa', 'receita'])],
            'valor' => ['required', 'numeric', 'min:0'],
            'descricao' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'date' => ['required', 'date'],
            'tipo_recorrencia' => ['nullable', Rule::in(['mensal', 'anual', 'diferente'])],
            'recorrencia_diferente_meses' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:tipo_recorrencia,diferente',
            ],
            'fim_da_recorrencia' => ['nullable', 'date', 'after_or_equal:data'],
            'esta_ativa' => ['boolean'],
        ];

    }
}
