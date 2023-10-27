<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRegistradoRequest extends FormRequest
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
            'apellido' => 'required',
            'direccion' => 'required',
            'codigo' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email' => 'El campo :attribute debe ser de tipo email',
            'required' => 'El campo es requerido', 
            'password.min' => 'La contraseña debe tener mínimo :min caracteres',
            // 'regex' => 'La contraseña debe contener al menos una mayúsculas, una minúscula, un número y alguno de estos caracteres (!, $, #, %, *).'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nombre',
            'apellido' => 'Apellido',
            'direccion' => 'Direccion',
            'codigo' => 'Código',
            'email' => 'Correo',
            'password' => 'Contraseña',
        ];
    }
}
