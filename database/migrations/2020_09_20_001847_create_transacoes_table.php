<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cedente_id');
            $table->unsignedBigInteger('beneficiario_id');
            $table->decimal('valor');
            $table->integer('status');
            $table->string('mensagem');
            $table->timestamps();

            $table->foreign('cedente_id')
                ->references('id')
                ->on('usuarios');

            $table->foreign('beneficiario_id')
                ->references('id')
                ->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transacoes');
    }
}
