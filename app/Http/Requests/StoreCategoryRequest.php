<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:categories,code',
            'type' => 'required|in:fonte,bloco,grupo,acao',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'code.max' => 'O código não pode ter mais de 50 caracteres.',
            'code.unique' => 'Este código já está sendo usado.',
            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo deve ser: fonte, bloco, grupo ou ação.',
            'parent_id.exists' => 'A categoria pai selecionada não existe.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }
}
