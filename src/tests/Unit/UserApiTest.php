<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{

	private $headers = [
		'Content-Type' => 'application/x-www-form-urlencoded',
        'X-Requested-With' => 'XMLHttpRequest'
	];

    /**
	 * Testando o método create
	 * Assegure-se que o não há nenhum usuário cadastrado com esse e-mail, ou então troque ele, pois o e-mail deve ser único
     *
     * @return void
     * @test
    */
    public function create()
    {

    	$user = [
    		'name' => 'José',
    		'rg' => '1312312',
    		'cpf' => '1312312',
    		'email' => 'jose@email.com',
    		'birth_date' => '1998-10-10',
    		'password' => '123456',
    	];

    	$response = $this->withHeaders($this->headers)->json('POST', '/api/users', $user);
        
        $response->assertStatus(201);
    }

    /**
	 * Testando os métodos get e show
     *
     * @return void
     * @test
    */
    public function get()
    {
    	$responseIndex = $this->withHeaders($this->headers)->json('GET', '/api/users');

    	$responseIndex->assertStatus(200);

    	$user = \App\User::first();

    	$responseShow = $this->withHeaders($this->headers)->json('GET', '/api/users/'.$user->id);

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
     * @test
    */
    public function update()
    {

    	$userUpdated = [
    		'name' => 'Gabriel',
    		'rg' => '1312312',
    		'cpf' => '1312312',
    		'email' => 'jose@email.com',
    		'birth_date' => '1998-10-10',
    		'password' => '123456',
    	];

    	$user = \App\User::first();

    	$response = $this->withHeaders($this->headers)->json('PUT', '/api/users/'.$user->id, $userUpdated);

    	$response->assertStatus(201);
    }  

    /**
	 * Testando o método delete
     *
     * @return void
     * @test
    */
    public function delete()
    {
    	$user = \App\User::first();

    	$response = $this->withHeaders($this->headers)->json('DELETE', '/api/users/'.$user->id);

        $response->assertStatus(204);
    }  
}
