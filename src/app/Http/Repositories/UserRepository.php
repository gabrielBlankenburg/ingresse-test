<?php

namespace App\Http\Repositories;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserRepository {

	/**
	 * Cria um novo usuário ou edita um existente
	 *
	 * @param \App\Http\Requests\UserRequest $request Request do tipo UserRequest
	 * @param int id optional O id do usuário, se esse parâmetro for passado este método tentará editar o usuário que contenha esse id
	 *
	 * @return instância de \App\User em caso de sucesso e false em caso de erro
	*/
	public static function save(UserRequest $request, $id = null) {

		$user;

		if ($id == null) {
			$user = new User();

			// Se for um novo usuário, é necessário informar a senha
			$request->validate(['password' => 'required']);
			
			if (!$request->validated()) {
				return false;
			}
		} else {
			$user = User::findOrFail($id);
		}


        $user->name = $request->input('name');
        $user->rg = $request->input('rg');
        $user->cpf = $request->input('cpf');
        $user->email = $request->input('email');
        $user->birth_date = $request->input('birth_date');

        if ($request->input('password') != null) {
        	$user->password = Hash::make($request->input('password'));
        }

        if ($user->save()) {
            return $user;
        } else {
            return false;
        }
	}
}