<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\OrcamentoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

    Route::controller(LancamentoController::class)->group(function () {
        Route::get('/lancamentos', 'index')->name('lancamentos.index');
        Route::get('/lancamentos/create', 'create')->name('lancamentos.create');
        Route::post('/lancamentos', 'store')->name('lancamentos.store');
        Route::put('/lancamentos/{id}', 'update')->name('lancamentos.update');
        Route::delete('/lancamentos/{id}', 'destroy')->name('lancamentos.destroy');
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
        Route::post('/metas', 'store')->name('metas.store');
        Route::get('/metas/{id}', 'show');
        Route::put('/metas/{id}', 'update');
        Route::delete('/metas/{id}', 'destroy');
    });

    Route::controller(OrcamentoController::class)->group(function () {
        Route::get('/orcamentos', 'index')->name('orcamentos.index');
        Route::post('/orcamentos', 'store')->name('orcamentos.store');
        Route::put('/orcamentos/{id}', 'update')->name('orcamentos.update');
        Route::delete('/orcamentos/{id}', 'destroy')->name('orcamentos.destroy');
    });

    Route::get('/lancamentos/import', [ImportController::class, 'index'])
        ->name('lancamentos.import');

    Route::post('/lancamentos/import/spreadsheets', [ImportController::class, 'importSpreadsheets'])
        ->name('lancamentos.import.spreadsheets');

    Route::post('/lancamentos/import/statements', [ImportController::class, 'importStatements'])
        ->name('lancamentos.import.statements');

    Route::get('/lancamentos/import/template', [ImportController::class, 'downloadTemplate'])
        ->name('lancamentos.import.template');

});
