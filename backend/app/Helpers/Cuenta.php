<?php


namespace App\Helpers;


class Cuenta
{
    public static function generaNumeroCuenta()
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10);
    }
}
