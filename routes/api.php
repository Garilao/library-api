<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);

    Route::get('/user', function(Request $r){ return $r->user(); });

    Route::get('/books', [BookController::class,'index']);
    Route::get('/books/{book}', [BookController::class,'show']);
    Route::post('/books', [BookController::class,'store']);
    Route::put('/books/{book}', [BookController::class,'update']);
    Route::delete('/books/{book}', [BookController::class,'destroy']);

    Route::post('/borrow', [BorrowController::class,'borrow']);
    Route::post('/return', [BorrowController::class,'return']);
    Route::get('/borrow/history', [BorrowController::class,'history']);
});
