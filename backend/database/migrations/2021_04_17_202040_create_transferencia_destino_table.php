<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciaDestinoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia_destino', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cuenta_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('transferencia_id');
            $table->foreign('cuenta_id')
                ->references('id')->on('cuenta')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('transferencia_id')
                ->references('id')->on('transferencia')
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
        Schema::dropIfExists('transferencia_destino');
    }
}
