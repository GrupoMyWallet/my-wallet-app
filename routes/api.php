<?php

use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    
    // Rotas para gráficos
    
    
});