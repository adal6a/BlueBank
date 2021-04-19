<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ErrorValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Error;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => User::all(),
            'message' => 'Listado de usuarios consultados correctamente'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $datosUsuario = $request->all();

        $validator = Validator::make($datosUsuario, [
            'nombre' => 'required',
            'identificacion' => 'required|unique:users,identificacion',
            'usuario' => 'required|unique:users,usuario',
            'correo' => 'required|unique:users,correo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                'message'=> 'Errores de validación'
            ]);
        }

        $datosUsuario['password'] = Hash::make($datosUsuario['password']);

        return response()->json([
            'success' => true,
            'data' => User::create($datosUsuario),
            'message' => 'Usuario guardado correctamente'
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Detalle del usuario consultado correctamente'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $usuario = User::find($id);

        if ($usuario) {
            $datosUsuario = $request->all();

            $validator = Validator::make($datosUsuario, [
                'nombre' => 'required',
                'identificacion' => 'required|unique:users,identificacion,' . $usuario->id,
                'usuario' => 'required|unique:users,usuario,' . $usuario->id,
                'correo' => 'required|unique:users,correo,' . $usuario->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                    'message'=> 'Errores de validación'
                ]);
            }

            $datosUsuario['password'] = Hash::make($datosUsuario['password']);

            $usuario->update($datosUsuario);

            return response()->json([
                'success' => true,
                'data' => $usuario,
                'message' => 'Usuario actualizado correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'El usuario no existe'
        ], 404);
    }
}
