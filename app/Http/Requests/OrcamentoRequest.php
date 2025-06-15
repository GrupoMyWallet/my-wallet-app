<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrcamentoRequest extends FormRequest
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

            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')->where(fn ($q) => $q->where('tipo', 'despesa')),
            ],
            'tipo' => 'required|in:anual,mensal',
            'ano' => 'required|integer|min:2000|max:2100',
            'valor' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [

            'categoria_id.required' => 'Categoria obrigatória.',
            'categoria_id.exists' => 'A categoria selecionada não existe ou não é do tipo "despesa".',

            'tipo.required' => 'O tipo de orçamento é obrigatório.',
            'tipo.in' => 'Tipo de orçamento inválido. Escolha entre anual ou mensal',

            'ano.required' => 'Ano obrigatório.',
            'ano.integer' => 'Ano inválido.',
            'ano.min' => 'Ano deve ser maior ou igual a 2000.',
            'ano.max' => 'Ano deve ser menor ou igual a 2100.',

            'mes.required_if' => 'O mês é obrigatório para exceções mensais.',
            'mes.integer' => 'Mês inválido.',
            'mes.min' => 'O mês deve ser entre 1 e 12.',
            'mes.max' => 'O mês deve ser entre 1 e 12.',

            'valor.required' => 'Valor do orçamento obrigatório.',
            'valor.numeric' => 'Valor do orçamento deve ser um número.',
            'valor.min' => 'O valor do orçamento não pode ser negativo.',
        ];
    }

    protected function prepareForValidation()
    {

        if ($this->tipo === 'anual') {
            $this->merge(['mes' => null]);
        }
    }
}
