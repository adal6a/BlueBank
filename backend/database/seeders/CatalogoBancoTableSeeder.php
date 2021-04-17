<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogoBanco;
class CatalogoBancoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CatalogoBanco::create([
            'nombre' => 'BlueBank',
            'activos' => 1000000,
        ]);
    }
}
