<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerfilUserRequest extends FormRequest
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
            'direccion' => 'required',
            'fijo' => 'required',
            'movil' => 'required',
            'avatar' => 'mimes:jpeg,jpg,png',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo es obligatorio.',
            'avatar.mimes' => 'El campo :attribute debe ser formato JPG, JPEG, PNG.',
        ];
    }

    public function attributes()
    {
        return [
            'avatar' => 'Imagen',
        ];
    }
}
