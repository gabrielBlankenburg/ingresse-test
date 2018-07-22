<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCpf;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => 'string|required',
            'last_name' => 'string|required',
            'cpf' => new ValidCpf,
            'rg' => 'string|max:14|nullable',
            'email' => 'email|required',
            'birth_date' => 'date|required',
        ];

        if ($this->method() != 'PUT') {
            $rules['email'] .= '|unique:users';
        }

        return $rules;
    }


}
