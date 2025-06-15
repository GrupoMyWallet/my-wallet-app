<?php

namespace Database\Seeders;

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
                'nome' => 'Salário',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Freelance',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Investimentos',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Vendas',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Aluguéis',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Outros Rendimentos',
                'tipo' => 'receita',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // CATEGORIAS DE DESPESA
            [
                'nome' => 'Moradia',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Alimentação',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Transporte',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Saúde',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Educação',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Entretenimento',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Roupas e Acessórios',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Eletrônicos',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Casa e Decoração',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Beleza e Cuidados',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Pets',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Impostos e Taxas',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Seguros',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Doações',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nome' => 'Viagens',
                'tipo' => 'despesa',
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

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
