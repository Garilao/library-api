<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use Illuminate\Http\Request;

// Public
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/books', [BookController::class,'store']);
        Route::put('/books/{book}', [BookController::class,'update']);
        Route::delete('/books/{book}', [BookController::class,'destroy']);
        Route::get('/borrow/history', [BorrowController::class,'history']);
    });

    // Everyone authenticated
    Route::get('/books', [BookController::class,'index']);
    Route::get('/books/{book}', [BookController::class,'show']);

    // Borrowing (users only)
    Route::middleware('role:student,admin')->group(function () {
        Route::post('/borrow', [BorrowController::class,'borrow']);
        Route::post('/return', [BorrowController::class,'return']);
    });
});
