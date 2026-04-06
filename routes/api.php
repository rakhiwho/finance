<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialRecordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ====================== PUBLIC ROUTES ======================
Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

// ====================== PROTECTED ROUTES ======================
Route::middleware('auth:sanctum')->group(function () {

    // Common user routes
    Route::get('/user', [UserController::class, 'profile']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::put('/user/{id}', [UserController::class, 'update']);

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::patch('/user/{id}/status', [UserController::class, 'updateStatus']);
        Route::patch('/user/{id}/role', [RoleController::class, 'updateRole']);

        // Financial Records - Full Control (Admin)
        Route::post('/records', [FinancialRecordController::class, 'store']);
        Route::put('/records/{id}', [FinancialRecordController::class, 'update']);
        Route::delete('/records/{id}', [FinancialRecordController::class, 'destroy']);
    });

    // Analyst + Admin (Read Only)
    Route::middleware('role:admin|analyst')->group(function () {
        Route::get('/users', [UserController::class, 'allUsers']);
        Route::get('/records', [FinancialRecordController::class, 'getFilteredData']);
        Route::get('/records/{id}', [FinancialRecordController::class, 'show']);
        Route::get('/dashboard/categories', [DashboardController::class, 'categoryTotals']);
        Route::get('/dashboard/trends', [DashboardController::class, 'trends']);
        Route::get('/dashboard/recent', [DashboardController::class, 'recent']);
    });

    // Dashboard routes (available to everyone authenticated)
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
  
});