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
     * @param  \App\Http\Repositories\UserRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepository $repository)
    {
        return response($repository->getAll(), 200);
    }

    /**
     * Salva um novo usuário
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Http\Repositories\UserRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request, UserRepository $repository)
    {
        $user = $repository->save($request);
        if ($user) {
            return response($user, 201);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }

    /**
     * Mostra o usuário com o id fornecido
     *
     * @param  \App\Http\Repositories\UserRepository $repository
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserRepository $repository, $id)
    {
        $user = $repository->get($id);
        return response($user, 200);
    }

    /**
     * Atualiza os dados de um usuário
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Http\Repositories\UserRepository $repository
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, UserRepository $repository, $id)
    {
        $user = $repository->save($request, $id);

        if ($user) {
            return response($user, 201);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }

    /**
     * Deleta um usuário
     *
     * @param  \App\Http\Repositories\UserRepository $repository
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRepository $repository, $id)
    {
        if ($repository->delete($id)) {
            return response([], 204);            
        } else {
            return response(['error' => 'Can\'t remove user'], 400);
        }
    }
}
