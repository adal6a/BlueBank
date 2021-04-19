<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorValidacion;
use App\Models\CatalogoBanco;
use App\Models\Cuenta;
use App\Models\Transaccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaccionController extends Controller
{
    public function deposito(Request $request)
    {
        $datosTransaccion = $request->all();
        $datosTransaccion['fecha_hora'] = Carbon::now();

        $validator = Validator::make($datosTransaccion, [
            'tipo' => 'required',
            'monto' => 'required|integer|min:1',
            'fecha_hora' => 'required',
            'cuenta_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                'message'=> 'Errores de validación'
            ]);
        }

        $transaccion = Transaccion::create($datosTransaccion);

        // Realiza la suma a la cuenta principal
        $this->realizaDepositoEnCuenta(
            $transaccion['cuenta_id'],
            $transaccion['monto']
        );

        return response()->json([
            'success' => true,
            'data' => $transaccion,
            'message' => 'Depósito realizado correctamente'
        ]);
    }

    private function realizaDepositoEnCuenta($cuenta, $monto)
    {
        $cuenta = Cuenta::where('id', $cuenta)->first();

        if ($cuenta) {
            $cuenta->balance = $cuenta->balance + $monto;
            $cuenta->save();

            $this->realizaTransaccionEnBanco($monto, 'deposito');
        }
    }

    // Suma al banco lo depositado por el usuario
    private function realizaTransaccionEnBanco($monto, $tipo)
    {
        $banco = CatalogoBanco::first();
        if ($tipo === 'deposito') {
            $banco->activos = $banco->activos + $monto;
            $banco->save();
        }
    }
}
