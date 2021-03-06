<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'v1'], function () use ($router) {

        $router->post('login', 'AuthController@login');

        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->get('logout', 'AuthController@logout');

            $router->post('user', 'UserController@store');
            $router->get('user', 'UserController@show');
            $router->put('user/{id}', 'UserController@update');
            $router->get('users', 'UserController@index');

            $router->post('cuenta', 'CuentaController@store');
            $router->put('cuenta/{id}', 'CuentaController@update');
            $router->post('cuentas/user', 'CuentaController@cuentasUser');

            $router->post('cuenta/transacciones', 'CuentaController@transacciones');

            $router->post('transaccion/deposito', 'TransaccionController@deposito');
            $router->post('transaccion/retiro', 'TransaccionController@retiro');
        });
    });
});
