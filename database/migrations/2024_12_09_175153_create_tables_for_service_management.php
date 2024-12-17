<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesForServiceManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabla de servicios
        Schema::create('servicios', function (Blueprint $table) {
            $table->id('servicio_id');
            $table->decimal('costo', 10, 2); // Costo del servicio
            $table->timestamps();
        });

        // Tabla de tarjetas de crédito
            Schema::create('tarjeta_creditos', function (Blueprint $table) {
                $table->id('tarjeta_id');
                $table->string('numero_tarjeta', 16)->unique(); // Número de tarjeta (16 dígitos)
                $table->date('fecha_expiracion');              // Fecha de expiración
                $table->string('cvc', 3);                      // Código CVC (3 dígitos)
                $table->decimal('saldo', 10, 2);               // Saldo disponible
                $table->timestamps();
            });

        // Tabla de compras
        Schema::create('compra', function (Blueprint $table) {
            $table->id('compra_id');
            $table->unsignedBigInteger('usuario_id'); // Usuario que realizó la compra
            $table->unsignedBigInteger('tarjeta_id'); // Tarjeta utilizada
            $table->decimal('monto', 10, 2); // Monto de la compra
            $table->timestamp('fecha_compra'); // Fecha de la compra
            $table->timestamps();

            // Relaciones
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->foreign('tarjeta_id')->references('tarjeta_id')->on('tarjeta_creditos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compra');
        Schema::dropIfExists('tarjeta_creditos');
        Schema::dropIfExists('servicios');
    }
}
