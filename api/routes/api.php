<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

Route::group(['prefix' => 'produtos'], function() {
    Route::get('', [ProdutoController::class, 'index']);
    Route::get('/{identifier}', [ProdutoController::class, 'show']);
    
    Route::group(['middleware' => 'admin'], function() {
        Route::post('', [ProdutoController::class, 'store']);
        Route::put('/{identifier}', [ProdutoController::class, 'update']);
    });
});

Route::group(['prefix' => 'pedidos', 'middleware' => 'admin'], function() {
    Route::get('/', [PedidoController::class, 'index']);
    Route::get('/{id}', [PedidoController::class, 'show']);
    Route::put('/{id}/status', [PedidoController::class, 'update']);
});

Route::middleware('auth')->post('/pedidos', [PedidoController::class, 'store']);