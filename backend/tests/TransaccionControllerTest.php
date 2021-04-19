<?php

use App\Models\CatalogoBanco;
use App\Models\User;
use Database\Factories\CuentaFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class TransaccionControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_usuario_realiza_deposito()
    {

        $usuario = User::factory()->create();
        $banco = CatalogoBanco::first();

        Passport::actingAs($usuario);

        $cuentaFactory = new CuentaFactory();

        $cuenta = $cuentaFactory->create([
            'numero' => mt_rand(),
            'balance' => mt_rand(1, 9999),
            'user_id' => $usuario->id,
            'catalogobanco_id' => $banco->id,
            'activo' => true
        ]);

        $response = $this->json('POST', '/api/v1/transaccion/deposito', [
            'tipo' => 'deposito',
            'monto' => $monto = mt_rand(1, 9999),
            'fecha_hora' => $fechaHora = \Carbon\Carbon::now(),
            'cuenta_id' => $cuenta->id
        ]);

        // Si se hizo la transaccion correcta
        $this->seeInDatabase('transaccion', [
            'monto' => $monto,
            'fecha_hora' => $fechaHora,
            'cuenta_id' => $cuenta->id
        ]);

        // Si se sumo a la cuenta la cantidad que se deposito
        $this->seeInDatabase('cuenta', [
            'id' => $cuenta->id,
            'balance' => $monto + $cuenta->balance
        ]);

        // Si se sumo a la cuenta principal del banco la cantidad que se deposito
        $this->seeInDatabase('catalogo_banco', [
            'id' => $banco->id,
            'activos' => $monto + $banco->activos
        ]);

        $response->assertResponseStatus(200);
    }
}
