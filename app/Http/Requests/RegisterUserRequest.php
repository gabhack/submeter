<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'tipo' => 'required',
            'contadores' => 'required|numeric|digits_between:1,10|min:1',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'El campo :attribute ya existe',
            'numeric' => 'El campo :attribute debe ser numérico',
            'email' => 'El campo :attribute debe ser de tipo email',
            'required' => 'Los campos son requeridos', 
            'password.min' => 'La contraseña debe tener mínimo :min caracteres',
            'contadores.min' => 'El campo :attribute debe ser mínimo :min',
            'regex' => 'La contraseña debe contener al menos una mayúsculas, una minúscula, un número y alguno de estos caracteres (!, $, #, %, *).'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Empresa',
            'password' => 'Contraseña',
            'tipo' => 'Tipo',
            'email' => 'Correo',
            'contadores' => 'Contadores',
        ];
    }
}