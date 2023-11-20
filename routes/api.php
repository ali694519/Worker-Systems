<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClientOrderController;
use App\Http\Controllers\WorkerReviewController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\AdminDashboard\PostStatusController;
use App\Http\Controllers\AdminDashboard\AdminNotificationController;
use App\Http\Controllers\{AdminController,ClientController,WorkerController,PostController};

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
Route::prefix('auth')->group(function() {
    Route::controller(AdminController::class)
    ->prefix('admin')
    ->group(function () {
        Route::post('/login',  'login');
        Route::post('/register',  'register');
        Route::post('/logout',  'logout');
        Route::post('/refresh',  'refresh');
        Route::get('/user-profile','userProfile');
    });
    Route::controller(WorkerController::class)
    ->prefix('worker')
    ->group( function () {
        Route::post('/login',  'login');
        Route::post('/register',  'register');
        Route::post('/logout',  'logout');
        Route::post('/refresh',  'refresh');
        Route::get('/user-profile',  'userProfile');
        Route::get('/verify/{token}',  'verify');
    });
    Route::controller(ClientController::class)
    ->prefix('client')
    ->group( function () {
        Route::post('/login','login');
        Route::post('/register',  'register');
        Route::post('/logout',  'logout');
        Route::post('/refresh',  'refresh');
        Route::get('/user-profile',  'userProfile');
    });
    });
    Route::get("/unauthorized",function() {
    return response()->json([
        "message"=>"unauthorized"
    ],401);
    })->name("login");


Route::post('/worker/review', [WorkerReviewController::class, 'store'])->middleware('auth:client');

Route::prefix('worker')
->middleware('auth:worker')
->group(function () {

    Route::get('pending/orders', [ClientOrderController::class, 'workerOrder']);
    Route::put('update/order/{id}', [ClientOrderController::class, 'update']);
    Route::get('/review/post/{postId}', [WorkerReviewController::class, 'postRate']);
    Route::get('/profile', [WorkerProfileController::class, 'userProfile']);
    Route::get('/profile/edit', [WorkerProfileController::class, 'edit']);
    Route::post('/profile/update', [WorkerProfileController::class, 'update']);
    Route::delete('/profile/posts/delete', [WorkerProfileController::class, 'delete']);
});


    Route::controller(PostController::class)
    ->prefix("worker/post/")
    ->group(function() {
        Route::post("/add","store")->middleware("auth:worker");
        Route::get("/all","index")->middleware("auth:admin");
        Route::get("/approved","approved");
        Route::get("/show/{post}","show");
    });

    Route::prefix('admin')->group(function () {
    Route::controller(PostStatusController::class)
    ->prefix('/post')->group(function () {
        Route::post('/status', 'changeStatus');
    });
    Route::controller(AdminNotificationController::class)
        ->middleware('auth:admin')
        ->prefix('admin/notifications')->group(function () {
            Route::get('/all', 'index');
            Route::get('/unread', 'unread');
            Route::post('/markReadAll', 'markReadAll');
            Route::delete('/deleteAll', 'deleteAll');
            Route::delete('/delete/{id}', 'delete');
        });
});

Route::prefix('client')->group(function () {
    Route::controller(ClientOrderController::class)->prefix('/order')->group(function () {
        Route::post('/request', 'addOrder')->middleware('auth:client');
        Route::get('/approved', 'approvedOrders')->middleware('auth:client');
    });

     Route::controller(PaymentController::class)->group(function () {
        Route::any('/checkout/{id}', 'checkout')->name('checkout');
        Route::get('/success', 'success')->name('checkout.success');
        Route::get('/cancel', 'cancel')->name('checkout.cancel');
    });

});

