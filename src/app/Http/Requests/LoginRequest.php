<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determina se o usuário tem permissão para fazer o request
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Pega as regras de validação para o request
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'email|required',
            'password' => 'string|min:6|required'  
        ];
    }
}
