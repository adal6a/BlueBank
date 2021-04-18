<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Helpers\ErrorValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{
    /**
     * Consulta las cuentas de un usuario
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function cuentasUser(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Cuenta::where('user_id', $request->user_id)->get(),
            'message' => 'Listado de cuentas consultadas correctamente'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $datosCuenta = $request->all();

        $validator = Validator::make($datosCuenta, [
            'numero' => 'required|integer|unique:cuenta,numero',
            'balance' => 'min:0',
            'user_id' => 'required',
            'catalogobanco_id' => 'required',
            'activo' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                'message'=> 'Errores de validación'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => Cuenta::create($datosCuenta),
            'message' => 'Cuenta guardada correctamente'
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
        $cuenta = Cuenta::find($id);

        if ($cuenta) {
            $datosCuenta = $request->all();

            $validator = Validator::make($datosCuenta, [
                'numero' => 'required|unique:users,numero,' . $cuenta->id,
                'balance' => 'min:0',
                'user_id' => 'required',
                'catalogobanco_id' => 'required',
                'activo' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                    'message'=> 'Errores de validación'
                ]);
            }

            $cuenta->update($datosCuenta);

            return response()->json([
                'success' => true,
                'data' => $cuenta,
                'message' => 'Cuenta actualizada correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'La cuenta no existe'
        ], 404);
    }
}
