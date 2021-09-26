<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavouritesController\FavouritesController;

  
Route::group(['middleware'=>'api','prefix'=>'favourites'],function ($router) {
    Route::post('/{postId}/add', [FavouritesController::class, 'addFavourite'])->middleware('auth:api');
    Route::get('/getPagenation', [FavouritesController::class, 'getFavourites']);
    Route::get('/withoutPagenation/get', [FavouritesController::class, 'findFavourites']);
    Route::delete('/{postId}/remove', [FavouritesController::class, 'deleteFavourite'])->middleware('auth:api');
});

