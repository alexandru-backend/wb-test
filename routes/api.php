<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataController;

Route::group([], function () {
    
    // Validamos a KEY aqui dentro manualmente para evitar o erro do Closure
    $key = request()->query('key');
    $validToken = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

    if ($key !== $validToken) {
        Route::any('{any}', function() {
            return response()->json(['error' => 'Unauthorized. Key is missing or invalid.'], 401);
        })->where('any', '.*');
    }

    // Rotas da API
    Route::get('/sales', [DataController::class, 'getSales']);
    Route::get('/orders', [DataController::class, 'getOrders']);
    Route::get('/stocks', [DataController::class, 'getStocks']);
    Route::get('/incomes', [DataController::class, 'getIncomes']);
});