<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Http\Requests\UserRequest;
use App\Http\Repositories\UserRepository;

class UsersController extends Controller
{
    /**
     * Lista todos os usuários cadastrados
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response($users, 200);
    }

    /**
     * Salva um novo usuário
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = UserRepository::save($request);
        if ($user) {
            return response($user, 201);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }

    /**
     * Mostra o usuário com o id fornecido
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response($user, 200);
    }

    /**
     * Atualiza os dados de um usuário
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = UserRepository::save($request, $id);

        if ($user) {
            return response($user, 201);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }

    /**
     * Deleta um usuário
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $delete = $user->delete();

        return response([], 204);
    }
}
