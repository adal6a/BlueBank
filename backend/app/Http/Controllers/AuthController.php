<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Token\Parser;

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

                return response()->json([
                    'success' => true,
                    'data' => [
                        'usuario' => $user,
                        'token' => $token,
                    ],
                    'message' => 'Sesión iniciada correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'La contraseña no coincide'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' =>'El usuario no existe'
            ]);
        }
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'La sesión ha sido cerrada'
        ]);
    }
}
