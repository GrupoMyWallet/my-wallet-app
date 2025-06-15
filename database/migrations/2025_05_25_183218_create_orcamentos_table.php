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
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['mensal', 'anual']);
            $table->year('ano');
            $table->tinyInteger('mes')->nullable(); // 1-12, null para orçamentos anuais
            $table->decimal('valor', 10, 2);
            $table->timestamps();

            // Índices para performance
            $table->index(['categoria_id', 'tipo', 'ano', 'mes']);
            $table->unique(['categoria_id', 'tipo', 'ano', 'mes'], 'unique_orcamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
