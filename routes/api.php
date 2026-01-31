<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

//Public routes - no auth
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

//Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//Admin routes -pretected
Route::middleware(['auth:sanctum','admin'])->group(function() {
	Route::post('/admin/products', [ProductController::class, 'store']);
	Route::put('/admin/products/{product}', [ProductController::class, 'update']);
	Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']); 
	});

// Cart API (guest + logged)
Route::middleware('optional.auth')->group(function() {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
});

//Orders (vale solo per logged users)
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    //Payments
    Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment']);
    
});