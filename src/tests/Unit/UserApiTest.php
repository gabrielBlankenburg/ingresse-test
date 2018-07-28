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

    private $token;

    /**
     * Chama os métodos na ordem certa
     *
     * @test
    */
    public function executeInOrder()
    {
        $this->unauthenticaded();
        $this->register();
        $this->create();
        $this->get();
        $this->update();
        $this->delete();
    }

    /**
     * Testando os métodos dos usuários antes de fazer a autenticação.
     * Os testes devem receber o status 401
     *
     * @return void
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
     * Testando o registro de um novo usuário e guardando o token
     *
     * @return void
    */
    public function register()
    {
        $user = [
            'name' => 'Admin',
            'last_name' => ' - Blankenburg',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'gabriel@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/register', $user);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'User created successfully']);

        $this->token = $response->decodeResponseJson('accessToken');
    }

    /**
	 * Testando o método create
	 * Assegure-se que o não há nenhum usuário cadastrado com esse e-mail, ou então troque ele, pois o e-mail deve ser único
     *
     * @return void
    */
    public function create()
    {

    	$user = [
    		'name' => 'Gabriel',
            'last_name' => 'Blankenburg',
    		'rg' => '1312312',
    		'cpf' => CpfValidation::generate(),
    		'email' => 'gabriel2@teste.com',
    		'birth_date' => '1998-10-10',
    		'password' => '123456',
    	];

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

    	$response = $this->withHeaders($headers)->json('POST', '/api/users', $user);
        
        $response->assertStatus(201);
    }

    /**
	 * Testando os métodos get e show
     *
     * @return void
    */
    public function get()
    {
        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;
        
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
	 * Testando o método update
     *
     * @return void
    */
    public function update()
    {

    	$userUpdated = [
            'name' => 'Gabriel',
            'last_name' => 'Gonçalves Blankenburg',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'gabriel@teste.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

    	$user = \App\User::first();

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

    	$response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

    	$response->assertStatus(201);
    }  

    /**
	 * Testando o método delete
     *
     * @return void
    */
    public function delete()
    {
    	$user = \App\User::first();

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

    	$response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id);

        $response->assertStatus(204);
    }  
}
