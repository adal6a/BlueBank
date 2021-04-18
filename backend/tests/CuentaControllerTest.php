<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class CuentaControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $usuario = User::factory()->create();
        Passport::actingAs($usuario);

        $response = $this->json('POST', '/api/v1/cuenta', [
            'numero' => $numero = rand(1, 9),
            'balance' => $balance = rand(1, 9),
            'user_id' => $usuario->id,
            'catalogobanco_id' => 1,
            'activo' => true
        ]);

        $this->seeInDatabase('cuenta', [
            'numero' => $numero,
            'balance' => $balance,
            'user_id' => $usuario->id,
        ]);

        $response->assertResponseStatus(200);
    }
}
