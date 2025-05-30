<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Lancamento;

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
        
        $base = Lancamento::rules();
        $rules = ['lancamentos' => 'required|array|min:1'];
        foreach ($base as $campo => $regra) {
            $rules["lancamentos.*.$campo"] = $regra;
        }
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $base = Lancamento::messages();
        $messages = [];
    
        foreach ($base as $rule => $msg) {
            if (strpos($rule, 'lancamentos.') === 0) {
                $messages[$rule] = $msg;
            } else {
                $messages["lancamentos.*.$rule"] = $msg;
            }
        }
    
        $messages['lancamentos.required'] = 'Adicione pelo menos um lançamento.';
        $messages['lancamentos.array'] = 'Os lançamentos devem ser enviados em formato de lista.';
    
        return $messages;
    }
}
