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

    public function __construct()
    {
        $this->middleware('guest');
    }

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

    public function register(UserRequest $request, UserRepository $repository)
    {
        $user = $repository->save($request);

        if ($user) {
            $token = $user->createToken('Personal')->accessToken;

            return response([
                'message' => 'User created successfully',
                'accessToken' => $token,
            ], 200);
        } else {
            return response(['error' => 'Can\'t add user'], 400);
        }
    }
}
