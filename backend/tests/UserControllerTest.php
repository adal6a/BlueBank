<?php

use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use Laravel\Passport\Passport;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_guarda_usuario()
    {
        Passport::actingAs(User::factory()->create());

        $response = $this->json('POST', '/api/v1/user', [
            'nombre' => $nombre = Str::random(10),
            'apellido' => $apellido = Str::random(10),
            'identificacion' => $identificacion = Str::random(10),
            'correo' => $correo = Str::random(10) . '@bluebank.com',
            'usuario' => $usuario = Str::random(10),
            'password' => 'password',
            'tipo' => 'cliente',
            'activo' => true,
        ]);

        $this->seeInDatabase('users', [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'identificacion' => $identificacion,
            'correo' => $correo,
            'usuario' => $usuario,
        ]);

        $response->assertResponseStatus(200);
    }
}
