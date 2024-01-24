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
	Route::post('eunomia/logout', [EunomiaController::class, 'logout']);
});