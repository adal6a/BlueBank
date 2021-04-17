<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->comment('Tipo: retiro o desposito');
            $table->bigInteger('monto');
            $table->timestamp('fecha_hora');
            $table->unsignedInteger('cuenta_id');
            $table->foreign('cuenta_id')
                ->references('id')->on('cuenta')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_transanccion');
    }
}
