<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Lancamento;

class UpdateLancamentoRequest extends FormRequest
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
        
        return Lancamento::rules();
        
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'O usuário é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser "despesa" ou "receita".',
            
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser pelo menos zero.',
            
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.max' => 'A descrição deve ter no máximo 255 caracteres.',
            
            'categoria_id.required' => 'A categoria é obrigatória.',
            'categoria_id.integer' => 'A categoria deve ser um número.',
            'categoria_id.exists' => 'A categoria selecionada não existe.',
            
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'A data deve ser válida.',
            
            'tipo_recorrencia.in' => 'O tipo de recorrência deve ser "none", "mensal", "anual" ou "diferente".',
            
            'recorrencia_diferente_meses.required_if' => 'O campo "recorrência diferente (meses)" é obrigatório quando o tipo de recorrência é "diferente".',
            'recorrencia_diferente_meses.integer' => 'O campo "recorrência diferente (meses)" deve ser um número inteiro.',
            'recorrencia_diferente_meses.min' => 'O campo "recorrência diferente (meses)" deve ser pelo menos 1.',
            
            'fim_da_recorrencia.date' => 'O fim da recorrência deve ser uma data válida.',
            
            'esta_ativa.boolean' => 'O campo "ativo" deve ser verdadeiro ou falso.',
        ];
    }
}
