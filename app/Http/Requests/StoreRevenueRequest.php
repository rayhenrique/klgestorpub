<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevenueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'date' => 'required|date|before_or_equal:today',
            'fonte_id' => 'required|exists:categories,id',
            'bloco_id' => 'required|exists:categories,id',
            'grupo_id' => 'required|exists:categories,id',
            'acao_id' => 'required|exists:categories,id',
            'observation' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição não pode ter mais de 255 caracteres.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'amount.max' => 'O valor não pode ser maior que R$ 999.999.999,99.',
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'A data deve ser uma data válida.',
            'date.before_or_equal' => 'A data não pode ser no futuro.',
            'fonte_id.required' => 'A fonte é obrigatória.',
            'fonte_id.exists' => 'A fonte selecionada não existe.',
            'bloco_id.required' => 'O bloco é obrigatório.',
            'bloco_id.exists' => 'O bloco selecionado não existe.',
            'grupo_id.required' => 'O grupo é obrigatório.',
            'grupo_id.exists' => 'O grupo selecionado não existe.',
            'acao_id.required' => 'A ação é obrigatória.',
            'acao_id.exists' => 'A ação selecionada não existe.',
            'observation.max' => 'A observação não pode ter mais de 1000 caracteres.',
        ];
    }
}
