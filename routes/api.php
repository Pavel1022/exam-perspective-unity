<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeyValueStoreController;
use App\Http\Controllers\Api\StackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/stack', [StackController::class, 'addToStack']);
Route::get('/stack', [StackController::class, 'getFromStack']);

Route::post('/key-value', [KeyValueStoreController::class, 'addKeyValue']);
Route::get('/key-value/{key}', [KeyValueStoreController::class, 'getKeyValue']);
Route::delete('/key-value/{key}', [KeyValueStoreController::class, 'deleteKeyValue']);