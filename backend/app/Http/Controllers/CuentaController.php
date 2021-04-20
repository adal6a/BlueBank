<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Helpers\Cuenta as CuentaHelper;
use App\Helpers\ErrorValidacion;
use App\Models\Transaccion;
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
        $datosCuenta['numero'] = CuentaHelper::generaNumeroCuenta();

        $validator = Validator::make($datosCuenta, [
            'numero' => 'unique:cuenta,numero',
            'balance' => 'integer|min:0',
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
                'balance' => 'integer|min:0',
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

    public function transacciones(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Transaccion::where('cuenta_id', $request->cuenta_id)->orderBy('fecha_hora', 'desc')->get(),
            'message' => 'Listado de transacciones consultadas correctamente'
        ]);
    }
}
