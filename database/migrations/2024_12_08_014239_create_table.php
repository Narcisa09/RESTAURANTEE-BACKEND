<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id'); // role_id SERIAL PRIMARY KEY
            $table->string('role_name', 50)->unique(); 
        });

        Schema::create('restaurantes', function (Blueprint $table) {
            $table->id('restaurante_id'); 
            $table->string('nombre_restaurante', 100);
            $table->string('direccion', 50)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('descripcion', 100)->nullable();
            $table->timestamps(); 
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('usuario_id'); 
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('email', 100)->unique();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('restaurante_id')->nullable();
            $table->timestamps();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->foreign('restaurante_id')->references('restaurante_id')->on('restaurantes')->onDelete('set null');
        });

        Schema::create('mesa', function (Blueprint $table) {
            $table->id('mesa_id'); 
            $table->integer('numero_mesa');
            $table->integer('capacidad');

            $table->boolean('disponibilidad')->default(true);

            $table->unsignedBigInteger('restaurante_id');
            $table->timestamps();
            $table->foreign('restaurante_id')->references('restaurante_id')->on('restaurantes')->onDelete('cascade');
        });


        Schema::create('reserva', function (Blueprint $table) {
            $table->id('reserva_id'); 
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('mesa_id');
            $table->timestamp('fecha_reserva'); 
            $table->string('estado'); 
            $table->timestamps();
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->foreign('mesa_id')->references('mesa_id')->on('mesa')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('reserva');
        Schema::dropIfExists('mesa');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('restaurantes');
        Schema::dropIfExists('roles');
    }
};