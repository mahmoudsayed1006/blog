<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\chatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    $actionId = "score_update";
    $actionData = array("team1_score"=> 46);
    event(new \App\Events\ActionEvent($actionId, $actionData));
    dd('Event fired.');
});
Route::post('sendmessage', [chatController::class,'sendMessage']);

Auth::routes();

