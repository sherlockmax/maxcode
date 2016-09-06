<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/setCash/{account}/{cash}', 'HomeController@setCash');
    Route::post('/gameData', 'HomeController@getGameData');
    Route::post('/finalCode/{games_no}', 'HomeController@getFinalCode');
    Route::post('/bet', 'HomeController@playerBet');
    Route::post('/betHistory', 'HomeController@getBetHistory');
});

Route::get('login', 'LoginController@index');
Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');
Route::get('user/{account}/{password}/{name}', 'LoginController@createUser');