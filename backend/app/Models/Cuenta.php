<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuenta';

    protected $guarded = ['id'];

    protected $casts = [
        'activo' => 'boolean'
    ];
}
