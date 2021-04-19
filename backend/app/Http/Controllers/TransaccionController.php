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
        [$validos, $datos] = $this->validaTransaccion($request->all());

        if ($validos) {
            $transaccion = Transaccion::create($datos);

            // Realiza la suma a la cuenta principal
            $cuenta = $this->realizaDepositoEnCuenta(
                $transaccion['cuenta_id'],
                $transaccion['monto']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'cuenta' => $cuenta,
                    'transaccion' => $transaccion
                ],
                'message' => 'Depósito realizado correctamente'
            ]);
        } else {
            return response()->json($datos);
        }
    }

    public function retiro(Request $request)
    {
        [$validos, $datos] = $this->validaTransaccion($request->all());
        if ($validos) {
            // Realiza la suma a la cuenta principal
            [$estado, $mensajesOCuenta] = $this->realizaRetiroEnCuenta(
                $datos['cuenta_id'],
                $datos['monto']
            );

            if ($estado) {
                $transaccion = Transaccion::create($datos);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'cuenta' => $mensajesOCuenta,
                        'transaccion' => $transaccion
                    ],
                    'message' => 'Retiro realizado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => $mensajesOCuenta,
                    'message'=> 'Errores de validación'
                ]);
            }
        } else {
            return response()->json($datos);
        }
    }

    private function validaTransaccion($datosTransaccion)
    {
        $datosTransaccion['fecha_hora'] = Carbon::now();

        $validator = Validator::make($datosTransaccion, [
            'tipo' => 'required',
            'monto' => 'required|integer|min:1',
            'fecha_hora' => 'required',
            'cuenta_id' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                false,
                [
                    'success' => false,
                    'errors' => ErrorValidacion::parseaErroresValidacion($validator->errors()),
                    'message'=> 'Errores de validación'
                ]
            ];
        }

        return [
            true,
            $datosTransaccion
        ];
    }

    private function realizaDepositoEnCuenta($cuenta, $monto)
    {
        $cuenta = Cuenta::where('id', $cuenta)->first();

        if ($cuenta) {
            $cuenta->balance = $cuenta->balance + $monto;
            $cuenta->save();

            $this->realizaTransaccionEnBanco($monto, 'deposito');
        }

        return $cuenta;
    }

    private function realizaRetiroEnCuenta($cuenta, $monto)
    {
        $cuenta = Cuenta::where('id', $cuenta)->first();

        if ($cuenta) {
            $restante = $cuenta->balance - $monto;

            // No se puede hacer un retiro mayor al balance de la cuenta
            if ($restante < 0) {
                return [
                    false,
                    [
                        'No se puede retirar una cantidad mayor a ' . $cuenta->balance
                    ]
                ];
            } else {
                $cuenta->balance = $restante;
                $cuenta->save();

                $this->realizaTransaccionEnBanco($monto, 'retiro');

                return [
                    true,
                    $cuenta
                ];
            }
        }

        return [
            false,
            [
                'La cuenta no existe'
            ]
        ];
    }

    // Suma al banco lo depositado por el usuario
    private function realizaTransaccionEnBanco($monto, $tipo)
    {
        $banco = CatalogoBanco::first();

        if ($banco) {
            if ($tipo === 'deposito') {
                $banco->activos = $banco->activos + $monto;
            } else {
                $banco->activos = $banco->activos - $monto;
            }

            $banco->save();
        }
    }
}
