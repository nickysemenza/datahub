<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/welcome','HomeController@showWelcome');

Route::get('/', array('as' => 'home', function() {
    return View::make('test');
}));

Route::get('/fbtest','FBController@fbtest');
Route::get('/thread/{threadID}','FBController@getFBMessagesFromThread');