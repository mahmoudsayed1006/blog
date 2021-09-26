<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController\CategoryController;

  
Route::group(['middleware'=>'api','prefix'=>'categories'],function ($router) {
    Route::post('/add', [CategoryController::class, 'addCategory'])->middleware('auth:api');
    Route::get('/getPagenation', [CategoryController::class, 'getCategories'])->middleware('auth:api');
    Route::get('/withoutPagenation/get', [CategoryController::class, 'findCategories']);
    Route::delete('/{id}', [CategoryController::class, 'deleteCategory'])->middleware('auth:api');
    Route::post('/{id}', [CategoryController::class, 'updateCategory'])->middleware('auth:api');
    Route::get('/{id}', [CategoryController::class, 'getCategoryById'])->name('findById');
});

