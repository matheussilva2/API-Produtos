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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->index('produtos_nome_index');
            $table->string('url_imagem');
            $table->string('sku')->index('produtos_sku_index');
            $table->decimal('preco', 10, 2);
            $table->boolean('ativo')->default(true)->index('produtos_ativo_index');
            $table->foreignId('criado_por')->constrained('usuarios');
            $table->timestamps();
            $table->index('created_at', 'produtos_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
