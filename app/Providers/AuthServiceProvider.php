<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Categoria::class => \App\Policies\CategoriaPolicy::class,
        \App\Models\Meta::class => \App\Policies\MetaPolicy::class,
        \App\Models\Orcamento::class => \App\Policies\OrcamentoPolicy::class,
        \App\Models\Lancamento::class => \App\Policies\LancamentoPolicy::class,
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
