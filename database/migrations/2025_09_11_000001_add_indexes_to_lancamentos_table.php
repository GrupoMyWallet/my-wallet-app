<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->index(['user_id', 'data']);
            $table->index('categoria_id');
            $table->index(['tipo', 'data']);
        });
    }

    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->dropIndex(['lancamentos_user_id_data_index']);
            $table->dropIndex(['lancamentos_categoria_id_index']);
            $table->dropIndex(['lancamentos_tipo_data_index']);
        });
    }
};
