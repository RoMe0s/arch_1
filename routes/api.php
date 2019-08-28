<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['prefix' => 'game'], function () {
        Route::post('start', 'GameController@startGame');
        Route::group(['prefix' => '{eloquentGame}'], function () {
            Route::get('', 'GameController@show');
            Route::post('join', 'GameController@join');
            Route::post('set-name', 'GameController@setName');
            Route::post('move', 'GameController@makeAMove');
        });
    });
});
