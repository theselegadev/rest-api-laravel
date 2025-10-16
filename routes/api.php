<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// 3|qEe6f8d7yfgHsKEXuALfeKPB5bXibRDePmVxlwTa0546e3ea

Route::get('users', [UserController::class,'index']);
Route::get('users/{id}', [UserController::class,'show'])->middleware('auth:sanctum');
Route::apiResource('payments', PaymentController::class)->middleware('auth:sanctum');
Route::post("login", [AuthController::class, 'login']);