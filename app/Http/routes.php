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
    return View::make('home');
}));


Route::group(['prefix' => 'fb'], function()
{
    Route::get('stickers','FBThreadDetailController@stickers');
    Route::get('','FBChatController@showThreads');
    Route::get('threads','FBChatController@showThreads');


    Route::get('thread/view/{thread_id}/{limit?}','FBThreadDetailController@getThread');
    Route::get('thread/special/{thread_id}/{query?}/{dateFormat?}','FBThreadDetailController@getSpecialThread');




    Route::get('json/threads','FBChatController@showThreadsJSON');
    Route::get('json/thread_wordcloud/{thread_id}','FBChatController@getThreadWordCloudJSON');

});




Route::get('/fb/config/extendToken/{token}','FBChatController@extendToken');
//old:
//Route::get('/fb/getThreads','FBChatController@getFBThreads');
//    Route::get('thread/update/{thread_id}/','FBChatController@getFBMessagesFromThread');
