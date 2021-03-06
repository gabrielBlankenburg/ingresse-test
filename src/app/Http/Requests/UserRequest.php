<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCpf;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => 'string|required',
            'last_name' => 'string|required',
            'cpf' => new ValidCpf,
            'rg' => 'string|max:14|nullable',
            'email' => 'email|required',
            'birth_date' => 'date|required',
            'admin' => 'boolean|nullable',
        ];

        if ($this->method() != 'PUT') {
            $rules['email'] .= '|unique:users';
        }

        return $rules;
    }


}
