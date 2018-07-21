<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    /**
	 * Testando o método create
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

    	$response = $this->withHeaders([
        	'Content-Type' => 'application/x-www-form-urlencoded',
        	'X-Requested-With' => 'XMLHttpRequest'
        ])->json('POST', '/api/users', $user);
        
        $response
            ->assertStatus(201);
    }
}
