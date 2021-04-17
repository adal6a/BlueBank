<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'nombre' => 'blue',
            'apellido' => 'bank',
            'identificacion' => '2411904960005C',
            'correo' => 'bluebank@bluesoft.com.co',
            'usuario' => 'bluebank',
            'password' => Hash::make('secret2021'),
            'tipo' => 'empleado',
            'activo' => true
        ]);
    }
}


