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
Route::get('/fb/','FBController@showThreads');

Route::get('/fb/getThreads','FBController@getFBThreads');
Route::get('/fbtest','FBController@fbtest');
Route::get('/thread/{thread_id}','FBController@getFBMessagesFromThread');
Route::get('/fb/threads','FBController@showThreads');
Route::get('/fb/thread/view/{thread_id}/{limit?}','FBController@getThread');
Route::get('/fb/thread/update/{thread_id}/','FBController@getFBMessagesFromThread');


Route::get('/fb/json/threads','FBController@showThreadsJSON');
Route::get('/fb/json/thread_wordcloud/{thread_id}','FBController@getThreadWordCloudJSON');

Route::get('/fb/thread/special/{thread_id}/{query?}/{dateFormat?}','FBController@getSpecialThread');

Route::get('/fb/extendToken/{token}','FBController@extendToken');
