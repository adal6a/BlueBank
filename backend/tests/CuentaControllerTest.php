<?php

use App\Models\User;
use Database\Factories\CuentaFactory;
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
    public function test_guarda_cuenta()
    {
        $usuario = User::factory()->create();
        Passport::actingAs($usuario);

        $response = $this->json('POST', '/api/v1/cuenta', [
            'balance' => $balance = mt_rand(1, 9999),
            'user_id' => $usuario->id,
            'catalogobanco_id' => 1,
            'activo' => true
        ]);

        $this->seeInDatabase('cuenta', [
            'balance' => $balance,
            'user_id' => $usuario->id,
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_actualiza_cuenta()
    {
        $usuario = User::factory()->create();
        Passport::actingAs($usuario);

        $cuentaFactory = new CuentaFactory();

        $cuenta = $cuentaFactory->create([
            'numero' => mt_rand(),
            'balance' => mt_rand(1, 9999),
            'user_id' => $usuario->id,
            'catalogobanco_id' => 1,
            'activo' => true
        ]);

        $response = $this->json('PUT', '/api/v1/cuenta/' . $cuenta->id, [
            'balance' => $cuenta->numero . 1,
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_lista_cuentas_usuario()
    {
        $usuario = User::factory()->create();
        Passport::actingAs($usuario);

        $cuentaFactory = new CuentaFactory();

        foreach ([1,2,3] as $i) {
            $cuentaFactory->create([
                'numero' => mt_rand() + $i,
                'balance' => mt_rand(1, 9999),
                'user_id' => $usuario->id,
                'catalogobanco_id' => 1,
                'activo' => true
            ]);
        }

        $response = $this->json('POST', '/api/v1/cuentas/user');

        $response->seeJson([
            'success' => true,
            'message' => 'Listado de cuentas consultadas correctamente'
        ]);

        $response->assertResponseStatus(200);
    }
}
