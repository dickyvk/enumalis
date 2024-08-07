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
	Route::post('zeus/profile', [ZeusController::class, 'setProfile']);
	Route::post('zeus/profile/{profile}', [ZeusController::class, 'setProfile']);
	Route::delete('zeus/profile/{profile}', [ZeusController::class, 'deleteProfile']);
	Route::get('zeus/notification', [ZeusController::class, 'getNotification']);
	Route::get('zeus/notification/{notification}', [ZeusController::class, 'showNotification']);
	Route::put('zeus/notification/{notification}', [ZeusController::class, 'updateNotification']);
	Route::delete('zeus/notification/{notification}', [ZeusController::class, 'deleteNotification']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
	Route::post('zeus/notification/send', [ZeusController::class, 'sendNotification']);
	Route::post('zeus/notification/blast', [ZeusController::class, 'blastNotification']);
});

use App\Http\Controllers\PhemeController;
Route::group(['middleware' => ['auth:sanctum']], function () {
	/**CATEGORY**/
    Route::get('pheme/category/', [PhemeController::class, 'getCategory']);
    Route::get('pheme/category/{category}', [PhemeController::class, 'showCategory']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('pheme/category/', [PhemeController::class, 'setCategory']);
        Route::patch('pheme/category/{category}', [PhemeController::class, 'setCategory']);
        Route::delete('pheme/category/{category}', [PhemeController::class, 'deleteCategory']);
    });
    // Threads by category
    Route::get('pheme/category/{category}/thread', [PhemeController::class, 'indexByCategory']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
    	Route::post('pheme/category/{category}/thread', [PhemeController::class, 'setThread']);
    });
    /**END OF CATEGORY**/

    /**THREAD**/
    Route::get('pheme/thread/recent', [PhemeController::class, 'recentThread']);
    Route::get('pheme/thread/unread', [PhemeController::class, 'unreadThread']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
    	Route::patch('pheme/thread/unread/mark-as-read', [PhemeController::class, 'markAsRead']);
    });
    Route::get('pheme/thread/{thread}', [PhemeController::class, 'showThread']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::delete('pheme/thread/{thread}', [PhemeController::class, 'deleteThread']);
        Route::post('pheme/thread/{thread}/restore', [PhemeController::class, 'restoreThread']);
    });
    // Posts by thread
    Route::get('pheme/thread/{thread}/post', [PhemeController::class, 'indexByThread'])->name('posts');
    Route::group(['middleware' => ['auth:sanctum']], function () {
    	Route::post('pheme/thread/{thread}/post', [PhemeController::class, 'setPost']);
    });
    /**END OF THREAD**/

	/**POST**/
    Route::group(['middleware' => ['auth:sanctum']], function () {
	    if (config('forum.api.enable_search')) {
	        Route::post('search', [PhemeController::class, 'search'])->name('search');
	    }
	});
    Route::get('pheme/post/recent', [PhemeController::class, 'recentPost']);
    Route::get('pheme/post/unread', [PhemeController::class, 'unreadPost']);
    Route::get('pheme/post/{post}', [PhemeController::class, 'showPost']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::patch('pheme/post/{post}', [PhemeController::class, 'setPost']);
        Route::delete('pheme/post/{post}', [PhemeController::class, 'deletePost']);
        Route::post('pheme/post/{post}/restore', [PhemeController::class, 'restorePost']);
    });
	/**END OF POST**/
});