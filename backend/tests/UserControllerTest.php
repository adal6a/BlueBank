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

    public function test_actualiza_usuario()
    {
        $usuario = User::factory()->create();

        Passport::actingAs($usuario);

        $response = $this->json('PUT', '/api/v1/user/' . $usuario->id, [
            'nombre' => $usuario->nombre . '-actualizado',
            'apellido' => $usuario->apellido . '-actualizado',
            'identificacion' => $usuario->identificacion . '-actualizado',
            'correo' => $usuario->correo . '-actualizado',
            'usuario' => $usuario->usuario . '-actualizado',
            'password' => 'password',
            'tipo' => 'cliente',
            'activo' => true,
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_lista_usuarios_paginados()
    {
        $usuario = User::factory()->create();

        Passport::actingAs($usuario);

        User::factory()->times(3)->create();

        $response = $this->json('GET', '/api/v1/users');

        $response->seeJson([
            'success' => true,
            'message' => 'Listado de usuarios consultados correctamente'
        ]);

        $response->assertResponseStatus(200);
    }

    public function test_detalle_usuario()
    {
        $usuario = User::factory()->create();

        Passport::actingAs($usuario);

        $response = $this->json('GET', '/api/v1/user');

        $response->seeJson([
            'success' => true
        ]);

        $response->assertResponseStatus(200);
    }
}
