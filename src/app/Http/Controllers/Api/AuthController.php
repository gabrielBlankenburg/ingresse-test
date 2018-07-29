<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    /**
     * Utiliza o middleware de guest
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
    }

    /**
     * Faz o login e recebe o token
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $attempt = Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')]);

        if ($attempt) {
            $user = Auth::user();

            $token = $user->createToken('Personal')->accessToken;

            return response([
                'message' => 'Logged In',
                'accessToken' => $token,
            ], 200);
        } else {
            return response(['error' => 'E-mail and Password don\'t match'], 401);
        }
    }

    /**
     * Gera um admin generico para que possa ser iniciado o sistema com algum admin
     *
     * @param  \App\Repositories\UserRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function generateAdmin(UserRepository $repository)
    {
        $user = $repository->generateAdmin();

        if ($user) {
            return response(['message' => 'Default admin generated'], 201);
        } else {
            return response(['error' => 'Can\'t add admin'], 400);
        }
    }

    /**
     * Cria um novo usuário e retorna o token
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Repositories\UserRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function register(UserRequest $request, UserRepository $repository)
    {
        $user = $repository->save($request);

        if ($user) {
            $token = $user->createToken('Personal')->accessToken;

            return response([
                'message' => 'User created successfully',
                'accessToken' => $token,
            ], 201);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }
}
