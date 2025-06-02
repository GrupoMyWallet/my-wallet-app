<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LancamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // Assumindo que existe um usuário com ID 1
        $now = Carbon::now();

        $lancamentos = [
            // RECEITAS
            [
                'user_id' => $userId,
                'tipo' => 'receita',
                'valor' => 5000.00,
                'descricao' => 'Salário',
                'categoria_id' => 1, // Categoria: Salário
                'data' => $now->copy()->subDays(25),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'receita',
                'valor' => 800.00,
                'descricao' => 'Freelance - Desenvolvimento Sistema',
                'categoria_id' => 2, // Categoria: Freelance
                'data' => $now->copy()->subDays(15),
                'tipo_recorrencia' => 'nenhuma',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'receita',
                'valor' => 150.00,
                'descricao' => 'Rendimento Poupança',
                'categoria_id' => 3, // Categoria: Investimentos
                'data' => $now->copy()->subDays(30),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // DESPESAS FIXAS
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 1200.00,
                'descricao' => 'Aluguel',
                'categoria_id' => 7, // Categoria: Moradia
                'data' => $now->copy()->subDays(28),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 150.00,
                'descricao' => 'Conta de Luz',
                'categoria_id' => 7, // Categoria: Moradia
                'data' => $now->copy()->subDays(20),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 89.90,
                'descricao' => 'Internet',
                'categoria_id' => 7, // Categoria: Moradia
                'data' => $now->copy()->subDays(18),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 45.90,
                'descricao' => 'Netflix',
                'categoria_id' => 12, // Categoria: Entretenimento
                'data' => $now->copy()->subDays(12),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 24.90,
                'descricao' => 'Spotify',
                'categoria_id' => 12, // Categoria: Entretenimento
                'data' => $now->copy()->subDays(10),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // DESPESAS PARCELADAS (usando recorrência)
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 166.67,
                'descricao' => 'Notebook Dell - Parcela 1/12',
                'categoria_id' => 14, // Categoria: Eletrônicos
                'data' => $now->copy()->subDays(30),
                'tipo_recorrencia' => 'diferente',
                'recorrencia_diferente_meses' => 1,
                'fim_da_recorrencia' => $now->copy()->addMonths(11),
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 83.33,
                'descricao' => 'Celular Samsung - Parcela 1/24',
                'categoria_id' => 14, // Categoria: Eletrônicos
                'data' => $now->copy()->subDays(25),
                'tipo_recorrencia' => 'diferente',
                'recorrencia_diferente_meses' => 1,
                'fim_da_recorrencia' => $now->copy()->addMonths(23),
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // DESPESAS VARIÁVEIS
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 450.00,
                'descricao' => 'Supermercado',
                'categoria_id' => 8, // Categoria: Alimentação
                'data' => $now->copy()->subDays(5),
                'tipo_recorrencia' => 'nenhuma',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 35.90,
                'descricao' => 'Almoço - Restaurante',
                'categoria_id' => 8, // Categoria: Alimentação
                'data' => $now->copy()->subDays(3),
                'tipo_recorrencia' => 'nenhuma',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 80.00,
                'descricao' => 'Combustível',
                'categoria_id' => 9, // Categoria: Transporte
                'data' => $now->copy()->subDays(7),
                'tipo_recorrencia' => 'nenhuma',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 120.00,
                'descricao' => 'Farmácia - Medicamentos',
                'categoria_id' => 10, // Categoria: Saúde
                'data' => $now->copy()->subDays(2),
                'tipo_recorrencia' => 'nenhuma',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // DESPESAS RECORRENTES NÃO MENSAIS
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 350.00,
                'descricao' => 'IPTU',
                'categoria_id' => 18, // Categoria: Impostos e Taxas
                'data' => $now->copy()->subDays(60),
                'tipo_recorrencia' => 'anual',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 1200.00,
                'descricao' => 'Seguro do Carro',
                'categoria_id' => 19, // Categoria: Seguros
                'data' => $now->copy()->subDays(90),
                'tipo_recorrencia' => 'anual',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // LANÇAMENTO INATIVO (exemplo de controle)
            [
                'user_id' => $userId,
                'tipo' => 'despesa',
                'valor' => 39.90,
                'descricao' => 'Amazon Prime (cancelado)',
                'categoria_id' => 12, // Categoria: Entretenimento
                'data' => $now->copy()->subDays(45),
                'tipo_recorrencia' => 'mensal',
                'recorrencia_diferente_meses' => null,
                'fim_da_recorrencia' => $now->copy()->subDays(15),
                'esta_ativa' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lancamentos')->insert($lancamentos);
    }
}