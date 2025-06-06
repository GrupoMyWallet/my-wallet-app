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
            $table->foreignId('categoria_id')->nullable()->constrained()->nullOnDelete();
            $table->date('data');
            $table->integer('intervalo_meses')->default(0); // 0=único, 1=mensal, 12=anual, 3=trimestral
            $table->date('fim_da_recorrencia')->nullable();
            $table->boolean('esta_ativa')->default(true);
            $table->timestamps();
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
