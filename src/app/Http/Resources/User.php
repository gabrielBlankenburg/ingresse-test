<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class User extends JsonResource
{
    /**
     * Transforma o resource em um array
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name.' '.$this->last_name,
            'email' => $this->email,
            'rg' => $this->rg,
            'cpf' => $this->cpf,
            'birth_date' => $this->birth_date,
            'admin' => $this->admin,
        ];
    }
}
