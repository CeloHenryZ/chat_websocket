<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([ 'middleware' => ['auth:sanctum'] ], function() {
    //user
    Route::get('/user/myUser', [UserController::class, 'myUser'])->name('user.myUser');
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');


    //messages
    Route::get('/messages/{user}', [MessageController::class, 'listMessages'])->name('messages.listMessages');
    Route::post('/message/sendMessage', [MessageController::class, 'sendMessage'])->name('message.sendMessage');
});
