<?php

namespace App\Helpers;

class ErrorValidacion
{
    public static function parseaErroresValidacion($errores)
    {
        $erroresParseados = array();

        foreach ($errores->toArray() as $error) {
            foreach ($error as $mensaje) {
                array_push($erroresParseados, $mensaje);
            }
        }

        return $erroresParseados;
    }
}
