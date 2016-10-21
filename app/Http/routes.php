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
    Route::post('/betHistory/{game_no}', 'HomeController@getBetHistory');
    Route::get('/record', 'RecordController@index');
    Route::get('/record/{games_no}', 'RecordController@record');
    Route::get('/settings', 'HomeController@settings');
    Route::post('/settings', 'HomeController@setSettings');
});

Route::get('/login', 'LoginController@index');
Route::post('/login', 'LoginController@login');
Route::get('/signup', 'LoginController@signUpPage');
Route::post('/signup', 'LoginController@signup');
Route::get('/logout', 'LoginController@logout');

//statistics
Route::get('/statistics', 'StatisticsController@index');
Route::post('/statistics_final_code', 'StatisticsController@finalCode');
Route::post('/dateList', 'StatisticsController@dateList');
Route::post('/gamesNoList', 'StatisticsController@gamesNoList');