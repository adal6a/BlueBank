<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Helpers\ErrorValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
}
