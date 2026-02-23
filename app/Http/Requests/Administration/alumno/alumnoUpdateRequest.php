<?php

namespace App\Http\Requests\Administration\alumno;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class alumnoUpdateRequest extends FormRequest
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
            'cedula' => [
                'required',
                'string',
                'min:5',
                'max:30',
                Rule::unique('alumnos', 'cedula')->ignore($this->alumno),
            ],
            'grado' => ['required', 'string', 'min:1', 'max:50'],
            'estado_prueba' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'La cédula ya está registrada.',
            'grado.required' => 'El grado es obligatorio.',
            'estado_prueba.required' => 'El estado de la prueba es obligatorio.',
        ];
    }
}
