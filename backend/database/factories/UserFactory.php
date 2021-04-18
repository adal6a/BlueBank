<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => Str::random(10),
            'apellido' => Str::random(10),
            'identificacion' => Str::random(10),
            'correo' => Str::random(10) . '@bluebank.com',
            'usuario' => Str::random(10),
            'password' => Hash::make('password'),
            'tipo' => 'empleado',
            'activo' => true,
        ];
    }
}
