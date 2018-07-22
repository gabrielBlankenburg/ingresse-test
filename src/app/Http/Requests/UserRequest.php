<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'id' => 'numeric|nullable',
            'name' => 'string|required',
            'last_name' => 'string|required',
            'cpf' => 'numeric|required',
            'rg' => 'numeric|nullable',
            'email' => 'email|required',
            'birth_date' => 'date|required',
            'password' => 'string|min:6|nullable',
        ];
    }


}
