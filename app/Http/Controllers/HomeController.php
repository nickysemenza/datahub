<?php namespace App\Http\Controllers;
use App\Models\Messages;
class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		var_dump(strtotime('2014-10-01T12:49:37+0000'));
		$messages = Messages::where('thread_id','=','t_mid.1411092543102:5cb624c43e1f3ada42')->orderBy('time_stamp', 'asc')->take(10)->get();
		//$messages = Messages::whereNotNull('time_stamp')->->get();
		foreach($messages as $eachMessage)
		{
			$arr=$eachMessage->toArray();
			//$arr['time_real']=strtotime($arr['time']);
			var_dump($arr);
		}
	}

}
