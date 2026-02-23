<?php

namespace App\Http\Requests\Administration\profesor;

use Illuminate\Foundation\Http\FormRequest;

class profesorStoreRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'cedula' => ['required', 'string', 'min:5', 'max:30', 'unique:profesores,cedula'],
            'grados_asignados' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'La cédula ya está registrada.',
            'grados_asignados.required' => 'Los grados asignados son obligatorios.',
        ];
    }
}
