<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

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

    public function test_usuario_cierra_sesion()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->json('GET', '/api/v1/logout');

        $response->assertResponseStatus(200);
    }
}
