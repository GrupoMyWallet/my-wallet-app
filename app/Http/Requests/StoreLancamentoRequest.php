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
            'lancamentos' => ['required', 'array', 'min:1'],

            'lancamentos.*.user_id' => ['required', 'exists:users,id'],
            'lancamentos.*.tipo' => ['required', Rule::in(['despesa', 'receita'])],
            'lancamentos.*.valor' => ['required', 'numeric', 'min:0'],
            'lancamentos.*.descricao' => ['required', 'string', 'max:255'],
            'lancamentos.*.categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'lancamentos.*.date' => ['required', 'date'],
            'lancamentos.*.tipo_recorrencia' => ['nullable', Rule::in(['none', 'mensal', 'anual', 'diferente'])],
            'lancamentos.*.recorrencia_diferente_meses' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:lancamentos.*.tipo_recorrencia,diferente',
            ],
            'lancamentos.*.fim_da_recorrencia' => ['nullable', 'date'],
            'lancamentos.*.esta_ativa' => ['boolean'],
        ];
    }
}
