<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $usuario = $request->usuario;
        $password = $request->password;

        $user = User::where('usuario', $usuario)->first();

        if ($user) {
            if (Hash::check($password, $user->password)) {
                $token = $user->createToken($user->correo)->accessToken;

                return response([
                    'success' => true,
                    'data' => [
                        'usuario' => $user,
                        'token' => $token,
                    ],
                    'message' => 'Logueado correctamente'
                ]);
            } else {
                return response([
                    'success' => false,
                    'data' => null,
                    'message' => 'La contraseña no coincide.'
                ], 422);
            }
        } else {
            return response([
                'success' => false,
                'data' => null,
                'message' =>'El usuario no existe.'
            ], 422);
        }
    }
}
