<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\EunomiaController;
Route::post('eunomia/register', [EunomiaController::class, 'register']);
Route::post('eunomia/login', [EunomiaController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::get('eunomia', [EunomiaController::class, 'show']);
	Route::post('eunomia', [EunomiaController::class, 'update']);
	Route::post('eunomia/rule', [EunomiaController::class, 'rule']);
	Route::post('eunomia/logout', [EunomiaController::class, 'logout']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
	Route::get('eunomia/all', [EunomiaController::class, 'showAll']);
	Route::post('eunomia/{user}', [EunomiaController::class, 'update']);
	Route::delete('eunomia/{user}', [EunomiaController::class, 'delete']);
});

use App\Http\Controllers\ZeusController;
Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::get('zeus/profile', [ZeusController::class, 'getProfile']);
	Route::post('zeus/profile/add', [ZeusController::class, 'addProfile']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
});