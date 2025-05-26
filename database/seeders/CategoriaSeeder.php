<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Alimentação', 'tipo' => 'despesa', 'user_id' => null],
            ['nome' => 'Transporte', 'tipo' => 'despesa', 'user_id' => null],
            ['nome' => 'Educação', 'tipo' => 'despesa', 'user_id' => null],
            ['nome' => 'Lazer', 'tipo' => 'despesa', 'user_id' => null],
            ['nome' => 'Delivery', 'tipo' => 'despesa', 'user_id' => null],
            ['nome' => 'Salário', 'tipo' => 'receita', 'user_id' => null],
            ['nome' => 'Freelance', 'tipo' => 'receita', 'user_id' => null],
            ['nome' => 'Venda', 'tipo' => 'receita', 'user_id' => null],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
