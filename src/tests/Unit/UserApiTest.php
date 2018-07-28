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

    private $email;

    /**
     * Chama os métodos na ordem certa
     *
     * @test
    */
    public function executeInOrder()
    {
        $this->unauthenticaded();
        $this->generateAdmin();
        $this->loginAsAdmin();
        $this->create(true);
        $this->register();
        $this->create();
        $this->get();
        $this->update($this->email);
        $this->delete($this->email);
        $this->loginAsAdmin();
        $this->create(true);
        $this->update($this->email, true);
        $this->delete($this->email, true);

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
        $email = 'jose@teste.com';
        $user = [
            'name' => 'José',
            'last_name' => 'Almeida',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => $email,
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/register', $user); 

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'User created successfully']);

        $this->token = $response->decodeResponseJson('accessToken');
        $this->email = $email;
    }

    /**
     * Gera um admin padrão
     *
     * @return void
    */
    public function generateAdmin()
    {
        $response = $this->withHeaders($this->headers)->json('POST', '/api/register-first-admin');

        $response->assertStatus(200);

        $response->assertJson(['message' => 'Default admin generated']);

    }

    /**
     * Loga como o admin gerado pelo app
     *
     * @return void
    */
    public function loginAsAdmin()
    {
        $email = 'gabriel@admin.com';
        $user = [
            'email' => $email,
            'password' => '123456',
        ];

        $response = $this->withHeaders($this->headers)->json('POST', '/api/login', $user);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'accessToken']);

        $response->assertJson(['message' => 'Logged In']);

        $this->token = $response->decodeResponseJson('accessToken');
        $this->email = $email;
    }

    /**
	 * Teste do método create, é possível utilizar um usuário admin ou não nesse teste
	 * Assegure-se que o não há nenhum usuário cadastrado com esse e-mail, ou então troque ele, pois o e-mail deve ser único
     *
     * @param boolean $admin diz se o usuário atual é adminstrador ou não
     * @return void
    */
    public function create($admin = false)
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
        
        if ($admin) {
            $response->assertStatus(201);            
        } else {
            $response->assertStatus(403);
        }
    }

    /**
	 * Teste dos métodos get e show
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
	 * Teste do método update, é possível testar com um usuário simples e um usuário administrador
     *
     * @param string $email informa o email do usuário atual
     * @param boolean $admin diz se o usuário atual é adminstrador ou não
     * @return void
    */
    public function update($email = null, $admin = false)
    {

    	$userUpdated = [
            'name' => 'User',
            'last_name' => '- Admin',
            'rg' => '1312312',
            'cpf' => CpfValidation::generate(),
            'email' => 'gabriel@admin.com',
            'birth_date' => '1998-10-10',
            'password' => '123456',
        ];

    	$user = \App\User::where('email', 'gabriel@admin.com')->first();

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

    	$response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

        if ($user->email == $email || $admin) {
    	   $response->assertStatus(201);            
        } else {
            $response->assertStatus(403);
        }

        $userUpdated = [
            'name' => 'José',
            'last_name' => 'Almeida',
            'rg' => '131231212',
            'cpf' => CpfValidation::generate(),
            'email' => 'jose@teste.com',
            'birth_date' => '1998-04-17',
            'password' => '123456',
        ];

        $user = \App\User::where('email', 'jose@teste.com')->first();

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

        $response = $this->withHeaders($headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

        if ($user->email == $email || $admin) {
           $response->assertStatus(201);            
        } else {
            $response->assertStatus(403);
        }
    }  

    /**
	 * Teste do método delete é possível fazer utilizando um usuário simples ou adminstrador
     *
     * @param string $email informa o email do usuário
     * @param boolean $admin diz se o usuário atual é adminstrador ou não
     * @return void
    */
    public function delete($email = null, $admin = false)
    {
        $user = \App\User::where('email', 'gabriel2@teste.com')->first();

        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$this->token;

        $response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id, $userUpdated);

        if ($user->email == $email|| $admin) {
           $response->assertStatus(204);           
        } else {
            $response->assertStatus(403);
        }

        $user = \App\User::where('email', 'jose@teste.com')->first();

        if (count($user) > 0) {
            $headers = $this->headers;
            $headers['Authorization'] = 'Bearer '.$this->token;

            $response = $this->withHeaders($headers)->json('DELETE', '/api/users/'.$user->id, $userUpdated);

            if ($user->email == $email || $admin) {
               $response->assertStatus(204);           
            } else {
                $response->assertStatus(403);
            }
        }
    }  
}
