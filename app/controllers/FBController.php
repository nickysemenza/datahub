<?php
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;
FacebookSession::setDefaultApplication(Config::get('keys.fb_appid'), Config::get('keys.fb_secret'));

class FBController extends BaseController {

public function fbtest()
{
    $token='CAAKfmL5GXZBgBANqSxZBDmZCzJHEqZBzz43gCcqXb40bjDmFaBH9pQ5c4dvFbzzbuRSo2qB3Df58ShZBIsYhrk9sKWHUr9VsdIDj5BhK9O5DWgzQgwZCSl2IkcvCYlenZBRiZCxE9kjHLKosNmKgXZAShrEhZAor7Q7mrx0YqntIRdtLtpjRoCf0m0KyjiSiyoTK5LeqFDrALqeqdQgGf0I0ZBZB';
//    $session = new FacebookSession($token);
//    $request = new FacebookRequest($session, 'GET', '/me');
//    $response = $request->execute();
//    $graphObject = $response->getGraphObject();
//    Clockwork::info($graphObject);
//    $data['fb']=$graphObject;

    $decoded=json_decode(file_get_contents("https://graph.facebook.com/me/threads?access_token=".$token), true);
    $data['fb_data'] = $decoded['data'];
    foreach($decoded['data'] as $eachThread)
    {
        $participants_ids=array();
        $participants_names=array();
        foreach($eachThread['participants']['data'] as $participants)
        {
            array_push($participants_ids,$participants['id']);
            array_push($participants_names,$participants['name']);
        }
        $info = Array('thread_id'=>$eachThread['id'],
                      'participants_ids'=>implode(",", $participants_ids),
                       'participants_names'=>implode(",", $participants_names),
        );
        $test=Threads::firstOrCreate($info);
        $test->message_count=$eachThread['message_count'];
        $test->save();
        Clockwork::info($info);
    }

    Clockwork::info($decoded['paging']);
    return View::make('test',compact('data'));
}
}
