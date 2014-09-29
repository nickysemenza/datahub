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
Route::get('/fb/','FBChatController@showThreads');

Route::get('/fb/getThreads','FBChatController@getFBThreads');
Route::get('/fbtest','FBChatController@fbtest');
Route::get('/thread/{thread_id}','FBChatController@getFBMessagesFromThread');
Route::get('/fb/threads','FBChatController@showThreads');
Route::get('/fb/thread/view/{thread_id}/{limit?}','FBChatController@getThread');
Route::get('/fb/thread/update/{thread_id}/','FBChatController@getFBMessagesFromThread');


Route::get('/fb/json/threads','FBChatController@showThreadsJSON');
Route::get('/fb/json/thread_wordcloud/{thread_id}','FBChatController@getThreadWordCloudJSON');

Route::get('/fb/thread/special/{thread_id}/{query?}/{dateFormat?}','FBChatController@getSpecialThread');

Route::get('/fb/extendToken/{token}','FBChatController@extendToken');
