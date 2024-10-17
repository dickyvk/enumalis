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
    Route::post('eunomia/logout', [EunomiaController::class, 'logout']);
	Route::get('eunomia', [EunomiaController::class, 'getUserDetails']);
	Route::post('eunomia', [EunomiaController::class, 'updateSelf']);
	Route::post('eunomia/rule', [EunomiaController::class, 'updateRule']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:admin']], function () {
    Route::get('eunomia/users', [EunomiaController::class, 'getAllUsers']);
    Route::post('eunomia/{user}', [EunomiaController::class, 'updateUser']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
	Route::get('eunomia/users', [EunomiaController::class, 'getAllUsers']);
	Route::post('eunomia/{user}', [EunomiaController::class, 'updateUser']);
	Route::delete('eunomia/{user}', [EunomiaController::class, 'deleteUserAccount']);
});

use App\Http\Controllers\ZeusController;
Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::get('zeus/profile', [ZeusController::class, 'getProfile']);
	Route::post('zeus/profile', [ZeusController::class, 'setProfile']);
	Route::post('zeus/profile/{profile}', [ZeusController::class, 'setProfile']);
	Route::delete('zeus/profile/{profile}', [ZeusController::class, 'deleteProfile']);
	Route::get('zeus/notification', [ZeusController::class, 'getNotification']);
	Route::get('zeus/notification/{notification}', [ZeusController::class, 'showNotification']);
	Route::put('zeus/notification/{notification}', [ZeusController::class, 'readNotification']);
	Route::delete('zeus/notification/{notification}', [ZeusController::class, 'deleteNotification']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:admin']], function () {
    Route::post('zeus/notification/send', [ZeusController::class, 'sendNotification']);
    Route::post('zeus/notification/blast', [ZeusController::class, 'blastNotification']);
});
Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
    Route::post('zeus/notification/send', [ZeusController::class, 'sendNotification']);
    Route::post('zeus/notification/blast', [ZeusController::class, 'blastNotification']);
});

use App\Http\Controllers\PhemeController;
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Categories
    Route::get('pheme/categories', [PhemeController::class, 'indexCategories']);
    Route::post('pheme/categories', [PhemeController::class, 'storeCategory']);
    Route::put('pheme/categories/{id}', [PhemeController::class, 'updateCategory']);
    Route::delete('pheme/categories/{id}', [PhemeController::class, 'destroyCategory']);

    // Threads
    Route::get('pheme/categories/{categoryId}/threads', [PhemeController::class, 'indexThreads']);
    Route::post('pheme/threads', [PhemeController::class, 'storeThread']);
    Route::put('pheme/threads/{id}', [PhemeController::class, 'updateThread']);
    Route::delete('pheme/threads/{id}', [PhemeController::class, 'destroyThread']);

    // Posts
    Route::get('pheme/threads/{threadId}/posts', [PhemeController::class, 'indexPosts']);
    Route::post('pheme/posts', [PhemeController::class, 'storePost']);
    Route::put('pheme/posts/{id}', [PhemeController::class, 'updatePost']);
    Route::delete('pheme/posts/{id}', [PhemeController::class, 'destroyPost']);
});
/*
use App\Http\Controllers\PhemeController;
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('pheme/{profile}/category/', [PhemeController::class, 'getCategory']);
    Route::get('pheme/{profile}/category/{category}', [PhemeController::class, 'showCategory']);
    Route::group(['middleware' => ['auth:sanctum', 'user-access:master']], function () {
        Route::post('pheme/category/', [PhemeController::class, 'setCategory']);
        Route::post('pheme/category/{category}', [PhemeController::class, 'setCategory']);
        Route::delete('pheme/category/{category}', [PhemeController::class, 'deleteCategory']);
        Route::post('pheme/profile/{profile}', [PhemeController::class, 'grantAccess']);
    });
    //Threads by category
    Route::get('pheme/category/{category}/thread', [PhemeController::class, 'indexByCategory']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
    	Route::post('pheme/category/{category}/thread/', [PhemeController::class, 'setThread']);
    	Route::post('pheme/category/{category}/thread/{thread}', [PhemeController::class, 'setThread']);
    });

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
    //Posts by thread
    Route::get('pheme/thread/{thread}/post', [PhemeController::class, 'indexByThread'])->name('posts');
    Route::group(['middleware' => ['auth:sanctum']], function () {
    	Route::post('pheme/thread/{thread}/post', [PhemeController::class, 'setPost']);
    });

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
});*/