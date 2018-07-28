<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\CpfValidation;

class UserApiTest extends TestCase
{

	private $headers = [
		'Content-Type' => 'application/x-www-form-urlencoded',
        'X-Requested-With' => 'XMLHttpRequest'
	];

    /**
     * Testando os métodos dos usuários antes de fazer a autenticação.
     * Os testes devem receber o status 401
     *
     * @return void
     * @test
    */
    public function unauthenticaded()
    {
        // Index
        $response = $this->withHeaders($this->headers)->json('GET', '/api/users');
        $response->assertStatus(401);

        // Store
        $response = $this->withHeaders($this->headers)->json('POST', '/api/users', []);
        $response->assertStatus(401);

        // Show
        $response = $this->withHeaders($this->headers)->json('GET', '/api/users/1');
        $response->assertStatus(401);

        // Update
        $response = $this->withHeaders($this->headers)->json('PUT', '/api/users/1');
        $response->assertStatus(401);

        // Destroy
        $response = $this->withHeaders($this->headers)->json('DELETE', '/api/users/1');
        $response->assertStatus(401);
    }

    /**
     * Gera um admin padrão
     *
     * @return void
     * @test
    */
    public function generateAdmin()
    {
        $response = $this->withHeaders($this->headers)->json('POST', '/api/register-first-admin');

        $response->assertStatus(200);

        $response->assertJson(['message' => 'Default admin generated']);

    }

    /**
     * Testando o registro de um novo usuário
     *
     * @return void
     * @test
    */
    public function register()
    {
        $user = [
            'name' => 'José',
            'last_name' => 'Almeida',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'jose@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/register', $user); 

        $response->assertStatus(201);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'User created successfully']);
    }

    /**
     * Loga como o admin gerado pelo app e faz um simples crud
     *
     * @return void
     * @test
    */
    public function loginAsAdminAndCRUD()
    {
        $user = [
            'email' => 'gabriel@admin.com',
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/login', $user);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'Logged In']);

        $token = $response->decodeResponseJson('accessToken');

        $email = 'gabriel2@teste.com';

        // Create
        $user = [
            'name' => 'Gabriel',
            'last_name' => 'Blankenburg',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => $email,
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ]; 

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$token;

        $response = $this->withHeaders($headers)->json('POST', '/api/users', $user);

        $response->assertStatus(201); 

        // Update
        $userUpdated = $user;
        $userUpdated['last_name'] = 'Gonçalves Blankenburg';

        $user = \App\User::where('email', $email)->first();

        $response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

        $response->assertStatus(201);

        // Delete
        $response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id);

        $response->assertStatus(204);

    }

    /**
	 * Teste dos métodos get e show
     *
     * @return void
     * @test
    */
    public function get()
    {
        $user = [
            'email' => 'jose@teste.com',
            'password' => '123456',
        ];


        $response = $this->withHeaders($this->headers)->json('POST', '/api/login', $user);

        $token = $response->decodeResponseJson('accessToken');

        $response->assertStatus(200);

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$token;

        $responseIndex = $this->withHeaders($headers)->json('GET', '/api/users');

        $responseIndex->assertStatus(200);

        $user = \App\User::first();


    	$responseShow = $this->withHeaders($headers)->json('GET', '/api/users/'.$user->id);

    	$responseShow
    		->assertStatus(200)
    		->assertJson([
                'id' => $user->id,
            ]);
    }  

    /**
     * Loga como usuário não admin e tenta fazer um simples crud
     *
     * @return void
     * @test
    */
    public function loginAsSimpleUserAndCrud()
    {
        $user = [
            'email' => 'jose@teste.com',
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/login', $user);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'Logged In']);

        $token = $response->decodeResponseJson('accessToken');

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$token;

        // Create
        $user = [
            'name' => 'Teste',
            'last_name' => 'Teste',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'teste.teste@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ]; 
        $response = $this->withHeaders($headers)->json('POST', '/api/users', $user);
        $response->assertStatus(403);

        // Update outro usuário
        $userUpdated = [
            'name' => 'José',
            'last_name' => 'Blankenburg',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'gabriel2@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

        $user = \App\User::where('email', '<>', 'jose@teste.com')->first();

        $response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

        $response->assertStatus(403);

        // Update a si mesmo
        $userUpdated = [
            'name' => 'José',
            'last_name' => 'Almeida',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'jose@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

        $user = \App\User::where('email', 'jose@teste.com')->first();

        $response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

        $response->assertStatus(201);

        // Deletar outro usuário
        $user = \App\User::where('email', '<>', 'jose@teste.com')->first();

        $response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id);

        $response->assertStatus(403);

        // Deletar a si mesmo
        $user = \App\User::where('email', 'jose@teste.com')->first();

        $response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id);

        $response->assertStatus(204);
    }
}
