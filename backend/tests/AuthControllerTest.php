<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_usuario_se_autentica()
    {
        $response = $this->json('POST', '/api/v1/login', [
            'usuario' => 'bluebank',
            'password' => 'secret2021'
        ]);

        $response->assertResponseStatus(200);
    }
}
