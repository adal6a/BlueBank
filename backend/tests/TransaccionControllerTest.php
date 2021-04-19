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
    protected $banco, $usuario, $cuenta;
    public function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->create();
        $this->banco = CatalogoBanco::first();

        $cuentaFactory = new CuentaFactory();
        $this->cuenta = $cuentaFactory->create([
            'numero' => mt_rand(),
            'balance' => mt_rand(1, 9999),
            'user_id' => $this->usuario->id,
            'catalogobanco_id' => $this->banco->id,
            'activo' => true
        ]);

        Passport::actingAs($this->usuario);
    }

    public function transaccion($monto, $fechaHora)
    {
        $this->seeInDatabase('transaccion', [
            'monto' => $monto,
            'fecha_hora' => $fechaHora,
            'cuenta_id' => $this->cuenta->id
        ]);
    }

    public function test_usuario_realiza_deposito()
    {
        $response = $this->json('POST', '/api/v1/transaccion/deposito', [
            'tipo' => 'deposito',
            'monto' => $monto = mt_rand(1, 9999),
            'fecha_hora' => $fechaHora = \Carbon\Carbon::now(),
            'cuenta_id' => $this->cuenta->id
        ]);

        // Si se hizo la transaccion correcta
        $this->transaccion($monto, $fechaHora);

        // Si se sumo a la cuenta la cantidad que se deposito
        $this->seeInDatabase('cuenta', [
            'id' => $this->cuenta->id,
            'balance' => $monto + $this->cuenta->balance
        ]);

        // Si se sumo a la cuenta principal del banco la cantidad que se deposito
        $this->seeInDatabase('catalogo_banco', [
            'id' => $this->banco->id,
            'activos' => $monto + $this->banco->activos
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_usuario_realiza_retiro()
    {
        $response = $this->json('POST', '/api/v1/transaccion/retiro', [
            'tipo' => 'retiro',
            'monto' => $monto = 10,
            'fecha_hora' => $fechaHora = \Carbon\Carbon::now(),
            'cuenta_id' => $this->cuenta->id
        ]);

        // Si se hizo la transaccion correcta
        $this->transaccion($monto, $fechaHora);

        // Si se sumo a la cuenta la cantidad que se deposito
        $this->seeInDatabase('cuenta', [
            'id' => $this->cuenta->id,
            'balance' => $this->cuenta->balance - $monto
        ]);

        // Si se sumo a la cuenta principal del banco la cantidad que se deposito
        $this->seeInDatabase('catalogo_banco', [
            'id' => $this->banco->id,
            'activos' => $this->banco->activos - $monto
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_usuario_no_puede_realizar_retiro_mayor_al_balance_actual()
    {
        $response = $this->json('POST', '/api/v1/transaccion/retiro', [
            'tipo' => 'retiro',
            'monto' => 100000000, // Numero exageradamente grande mayor que mt_rand(1, 9999)
            'fecha_hora' => \Carbon\Carbon::now(),
            'cuenta_id' => $this->cuenta->id
        ]);

        $response->seeJson([
            'success' => false
        ]);

        $response->assertResponseStatus(200);
    }
}
