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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->index('usuario_id', 'pedidos_usuario_id_index');
            $table->enum('status', ['CRIADO', 'PAGO', 'CANCELADO'])->default('CRIADO');
            
            $table->string('logradouro_entrega');
            $table->string('cidade_entrega');
            $table->string('estado_entrega');
            
            $table->decimal('valor_frete', 10, 2);
            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
