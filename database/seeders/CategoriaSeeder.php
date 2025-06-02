<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            // CATEGORIAS DE RECEITA
            [
                'id' => 1,
                'nome' => 'Salário',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nome' => 'Freelance',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nome' => 'Investimentos',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nome' => 'Vendas',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'nome' => 'Aluguéis',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'nome' => 'Outros Rendimentos',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // CATEGORIAS DE DESPESA
            [
                'id' => 7,
                'nome' => 'Moradia',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'nome' => 'Alimentação',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'nome' => 'Transporte',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'nome' => 'Saúde',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'nome' => 'Educação',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'nome' => 'Entretenimento',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'nome' => 'Roupas e Acessórios',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'nome' => 'Eletrônicos',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'nome' => 'Casa e Decoração',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'nome' => 'Beleza e Cuidados',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'nome' => 'Pets',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'nome' => 'Impostos e Taxas',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 19,
                'nome' => 'Seguros',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'nome' => 'Doações',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 21,
                'nome' => 'Viagens',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 22,
                'nome' => 'Outros Gastos',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categorias')->insert($categorias);
    }
}