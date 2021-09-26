<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
Route::group(['middleware'=>'api'],function ($router) {
    Route::post('signUp', [UserController::class, 'signUp']);
    Route::post('login', [UserController::class, 'login']);
    Route::put('refresh', [UserController::class, 'refresh']);
    Route::put('logout', [UserController::class, 'logout'])->middleware('auth:api');
    Route::get('users', [UserController::class, 'getUsers'])->middleware('auth:api');
    Route::post('sendMail',[UserController::class, 'sendMail'])->name('sendEmail');
    Route::post('upload',[UserController::class, 'upload'])->name('uploadImg');
    Route::delete('/{id}', [UserController::class, 'deleteUser'])->middleware('auth:api');
    Route::post('/{id}', [UserController::class, 'updateUser'])->middleware('auth:api');
    Route::get('/{id}', [UserController::class, 'getUserById'])->name('findById');
    Route::get('/notifications/getPagentation', [UserController::class, 'getNotifs'])->name('getNotifs');
    Route::post('forgetPassword/email',[UserController::class, 'forgetPassword'])->name('forgetPassword');
    Route::post('confirmCode/email',[UserController::class, 'verifyCode'])->name('verifyCode');
    Route::post('restPassword/email',[UserController::class, 'restPassword'])->name('restPassword');
    Route::get('/push-notificaiton', [UserController::class, 'getToken'])->name('push-notificaiton');
    Route::post('/store-token', [UserController::class, 'storeToken'])->name('store.token');
    Route::put('/sendNotification', [UserController::class, 'sendNotif'])->middleware('auth:api');
});

