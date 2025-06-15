<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            // Receitas
            [
                'user_id' => 1,
                'tipo' => 'receita',
                'valor' => 5000.00,
                'descricao' => 'Salário',
                'categoria_id' => 1,
                'data' => Carbon::now()->startOfMonth(),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'receita',
                'valor' => 800.00,
                'descricao' => 'Freelance desenvolvimento',
                'categoria_id' => 2,
                'data' => Carbon::now()->subDays(5),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'receita',
                'valor' => 150.00,
                'descricao' => 'Dividendos',
                'categoria_id' => 3,
                'data' => Carbon::now()->subDays(10),
                'intervalo_meses' => 3,
                'fim_da_recorrencia' => Carbon::now()->addYear(),
                'esta_ativa' => true,
            ],

            // Despesas fixas
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 1200.00,
                'descricao' => 'Aluguel',
                'categoria_id' => 4,
                'data' => Carbon::now()->startOfMonth(),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 89.90,
                'descricao' => 'Internet',
                'categoria_id' => 5,
                'data' => Carbon::now()->day(15),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 45.00,
                'descricao' => 'Netflix',
                'categoria_id' => 6,
                'data' => Carbon::now()->day(20),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],

            // Despesas variáveis
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 350.00,
                'descricao' => 'Supermercado',
                'categoria_id' => 7,
                'data' => Carbon::now()->subDays(3),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 120.00,
                'descricao' => 'Combustível',
                'categoria_id' => 8,
                'data' => Carbon::now()->subDays(7),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 80.00,
                'descricao' => 'Farmácia',
                'categoria_id' => 9,
                'data' => Carbon::now()->subDays(12),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],

            // Despesas anuais
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 1200.00,
                'descricao' => 'IPVA',
                'categoria_id' => 10,
                'data' => Carbon::now()->month(3)->day(15),
                'intervalo_meses' => 12,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 800.00,
                'descricao' => 'Seguro do carro',
                'categoria_id' => 11,
                'data' => Carbon::now()->month(6)->day(10),
                'intervalo_meses' => 12,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],

            // Investimentos
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 500.00,
                'descricao' => 'Investimento CDB',
                'categoria_id' => 12,
                'data' => Carbon::now()->day(5),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],

            // Lazer
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 200.00,
                'descricao' => 'Cinema e jantar',
                'categoria_id' => 13,
                'data' => Carbon::now()->subDays(2),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],

            // Educação
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 299.00,
                'descricao' => 'Curso online',
                'categoria_id' => 14,
                'data' => Carbon::now()->subDays(15),
                'intervalo_meses' => 1,
                'fim_da_recorrencia' => Carbon::now()->addMonths(6),
                'esta_ativa' => true,
            ],

            // Saúde
            [
                'user_id' => 1,
                'tipo' => 'despesa',
                'valor' => 180.00,
                'descricao' => 'Consulta médica',
                'categoria_id' => 15,
                'data' => Carbon::now()->subDays(8),
                'intervalo_meses' => 0,
                'fim_da_recorrencia' => null,
                'esta_ativa' => true,
            ],
        ];

        DB::table('lancamentos')->insert($lancamentos);
    }
}
