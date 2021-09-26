<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RateController\RateController;

  
Route::group(['middleware'=>'api','prefix'=>'rate'],function ($router) {
    Route::post('/{postId}/add', [RateController::class, 'addRate'])->middleware('auth:api');
    Route::get('/getPagenation', [RateController::class, 'getRates']);
    Route::get('/withoutPagenation/get', [RateController::class, 'findRates']);
    Route::post('/{id}/updateRate', [RateController::class, 'updateRate'])->middleware('auth:api');
    Route::delete('/{id}', [RateController::class, 'deleteRate'])->middleware('auth:api');
});

