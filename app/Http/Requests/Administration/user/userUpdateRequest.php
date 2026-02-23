<?php

namespace App\Http\Requests\Administration\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class userUpdateRequest extends FormRequest
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
            'email' => [
                'required',
                'email', Rule::unique('users', 'email')->ignore($this->user),
                'min:8',
            ],
            'name' => ['required','max:50','min:2'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El email es obligatorio.',
            'email.min' => 'El email es de minimo 8 caracteres.',
            'email.unique' => 'El email ya esta registrado.',
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre es de maximo 50 caracteres',
            'name.min' => 'El nombre es de minimo 2 caracteres',
        ];
    }
}
