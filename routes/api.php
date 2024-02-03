<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EunomiaController;

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

Route::post('eunomia/register', [EunomiaController::class, 'register']);
Route::post('eunomia/login', [EunomiaController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::get('eunomia', [EunomiaController::class, 'index']);
	Route::post('eunomia/logout', [EunomiaController::class, 'logout']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
	Route::get('eunomia/{user}', [EunomiaController::class, 'show']);
});