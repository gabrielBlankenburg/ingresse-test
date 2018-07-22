<?php

namespace App\Http\Repositories;

use App\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserRepository {

	protected $expiration = 60 * 24;
	protected $userKey = 'user_';
	protected $usersKey = 'users';

	/**
	 * Cria um novo usuário ou edita um existente
	 *
	 * @param \App\Http\Requests\UserRequest $request Request do tipo UserRequest
	 * @param int id optional O id do usuário, se esse parâmetro for passado este método tentará editar o usuário que contenha esse id
	 *
	 * @return instância de \App\User em caso de sucesso e false em caso de erro
	*/
	public function save(UserRequest $request, $id = null) {

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
        $user->last_name = $request->input('last_name');
        $user->rg = $request->input('rg');
        $user->cpf = $request->input('cpf');
        $user->email = $request->input('email');
        $user->birth_date = $request->input('birth_date');

        if ($request->input('password') != null) {
        	$user->password = Hash::make($request->input('password'));
        }

        if ($user->save()) {

        	$this->clearUsersCache();

        	if ($id !== null) {
        		$this->clearUserCache($id);
        	}

            return new UserResource($user);
        } else {
            return false;
        }
	}

	/**
	 * Retorna a lista de todos os usuários salvo no cache, se não houver registro no cache uma consulta é feita no banco, e então os registros são saçvos no cache por 24 horas
	 *
	 * @return Instância de \App\User
	*/
	public function getAll()
	{
		return Cache::remember($this->usersKey, $this->expiration, function() {
			return UserResource::collection(User::all());
		});
	}

	/**
	 * Mostra os detalhes de um usuário salvo no cache, se não houver a informação do usuário em questão salva no cache uma consulta é feita no banco, salvando no cache a informação dessa consulta e a retornando
	 *
	 * @param int id
	 * @return instância de \App\User
	*/
	public function get($id)
	{
		$key = $this->userKey.$id;

		return Cache::remember($key, $this->expiration, function() use ($id){
			return new UserResource(User::findOrFail($id));
		});
	}

	/**
	 * Deleta um usuário e remove o mesmo do cache
	 *
	 * @param int id
	 * @return true caso o usuário seja removido ou false em caso de erro
	*/
	public function delete($id)
	{
		$user = User::findOrFail($id);

        if ($user->delete()) {
        	$this->clearUserCache($id);
        	$this->clearUsersCache();
	        return true;
        } else {
        	return false;
        }
	}

	/**
	 * Limpa o cache de um usuário
	 *
	 * @param int $id
	*/
	protected function clearUserCache($id)
	{
		Cache::forget('user_'.$id);
	}

	/**
	 * Limpa o cache de todos os usuários
	 *
	 * @param int $id
	*/
	protected function clearUsersCache()
	{
		Cache::forget('users');
	}
}