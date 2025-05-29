<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LancamentoController;

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

});
