<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController\PostController;

  
Route::group(['middleware'=>'api','prefix'=>'posts'],function ($router) {
    Route::post('/add', [PostController::class, 'addPost']);
    Route::get('/getPagenation', [PostController::class, 'getPosts'])->middleware('auth:api');
    Route::get('/withoutPagenation/get', [PostController::class, 'findPosts']);
    Route::delete('/{id}', [PostController::class, 'deletePost'])->middleware('auth:api');
    Route::post('/{id}', [PostController::class, 'updatePost'])->middleware('auth:api');
    Route::get('/{id}', [PostController::class, 'getPostById'])->name('findById');
});

