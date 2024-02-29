<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user_create',[UserController::class,'createUser']);

Route::middleware('auth:api')->group(function(){
    Route::get('/user_all',[UserController::class,'allUser']);
    Route::get('/user_view/{id}',[UserController::class,'viewUser']);
    Route::put('/user_edit/{id}',[UserController::class,'edit']);
    Route::put('/user_softdel/{id}',[UserController::class,'softdel']);
    Route::put('/user_restore/{id}',[UserController::class,'restore']);
    Route::delete('/user_delete/{id}',[UserController::class,'delete']);
    Route::get('/user_logout',[UserController::class,'logout']);
});
Route::post('/login',[UserController::class,'login']);


