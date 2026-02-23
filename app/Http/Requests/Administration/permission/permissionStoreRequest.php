<?php

namespace App\Http\Requests\Administration\permission;

use Illuminate\Foundation\Http\FormRequest;

class permissionStoreRequest extends FormRequest
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
            'name' => ['required','unique:permissions,name','max:50','min:2'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'El nombre ya esta registrado',
            'name.max' => 'El nombre es de maximo 50 caracteres',
            'name.min' => 'El nombre es de minimo 2 caracteres',
        ];
    }
}
