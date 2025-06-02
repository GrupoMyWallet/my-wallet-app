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
        Schema::create('lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('tipo', ['despesa', 'receita']);
            $table->decimal('valor', 12, 2);
            $table->string('descricao');
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->date('data');
            $table->enum('tipo_recorrencia', ['nenhuma', 'mensal', 'anual', 'diferente'])->default('nenhuma');
            $table->integer('recorrencia_diferente_meses')->nullable();
            $table->date('fim_da_recorrencia')->nullable();
            $table->boolean('esta_ativa')->default(true);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos');
    }
};
