<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MetaController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::controller(LancamentoController::class)->group(function () {
        Route::get('/lancamentos', 'index')->name('lancamentos.index');
        Route::get('/lancamentos/create', 'create');
        Route::post('/lancamentos', 'store')->name('lancamentos.store');
        Route::get('/lancamentos/{id}', 'show');
        Route::put('/lancamentos/{id}', 'update');
        Route::delete('/lancamentos/{id}', 'destroy');  
    });

    Route::controller(CategoriaController::class)->group(function () {
        Route::get('/categorias', 'index')->name('categorias.index');
        Route::post('/categorias', 'store')->name('categorias.store');
        Route::get('/categorias/{id}', 'show');
        Route::put('/categorias/{id}', 'update');
        Route::delete('/categorias/{id}', 'destroy'); 
    });

    Route::controller(MetaController::class)->group(function () {
        Route::get('/metas', 'index')->name('metas.index');
        Route::get('/metas', 'create')->name('metas.create');
        Route::post('/metas', 'store')->name('metas.store');
        Route::get('/metas/{id}', 'show');
        Route::put('/metas/{id}', 'update');
        Route::delete('/metas/{id}', 'destroy'); 
    });

    Route::controller(OrcamentoController::class)->group(function () {
        Route::get('/orcamentos', 'index')->name('orcamentos.index');
        Route::get('/orcamentos', 'create')->name('orcamentos.create');
        Route::post('/orcamentos', 'store')->name('orcamentos.store');
        Route::get('/orcamentos/{id}', 'show');
        Route::put('/orcamentos/{id}', 'update');
        Route::delete('/orcamentos/{id}', 'destroy'); 
    });

});
