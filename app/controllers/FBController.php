<?php
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;
FacebookSession::setDefaultApplication(Config::get('keys.fb_appid'), Config::get('keys.fb_secret'));

class FBController extends BaseController {
//    $session = new FacebookSession($token);
//    $request = new FacebookRequest($session, 'GET', '/me');
//    $response = $request->execute();
//    $graphObject = $response->getGraphObject();
//    Clockwork::info($graphObject);
//    $data['fb']=$graphObject;
public function getFBThreads()
{
    $token='CAAKfmL5GXZBgBANqSxZBDmZCzJHEqZBzz43gCcqXb40bjDmFaBH9pQ5c4dvFbzzbuRSo2qB3Df58ShZBIsYhrk9sKWHUr9VsdIDj5BhK9O5DWgzQgwZCSl2IkcvCYlenZBRiZCxE9kjHLKosNmKgXZAShrEhZAor7Q7mrx0YqntIRdtLtpjRoCf0m0KyjiSiyoTK5LeqFDrALqeqdQgGf0I0ZBZB';
    $data=0;
    $url="https://graph.facebook.com/me/threads?access_token=".$token;
    for($x=0;$x<30;$x++)
    {
        $decoded=json_decode(file_get_contents($url), true);
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
                'participants_names'=>preg_replace('/[^a-z0-9_, ]/i', '', utf8_encode(implode(",", $participants_names))),
            );
            $test=Threads::firstOrCreate($info);
            $test->message_count=$eachThread['message_count'];
            $test->save();
        }
        $url=$decoded['paging']['next'];
    }
}
public function getFBMessagesFromThread($threadID)
{
    $data=$threadID;
    $token='CAAKfmL5GXZBgBADCiSlBf5LM9ZC4GcbDFFazfbSoXTkNAZBdAteuNkDZBzueGQFa3618CRhnKVlBndR6JPx9wOv2KGoLBt8y0bbEFyEF6Ez8akkXPtwGhwcuhNa8AzRIlZCGDcL9440RBNnRAClWd0ECCM1NuvmCEoCzLKdZClxUZCUK1vtZAUfS6eIZBZBLf6RFpZBIl8wZBbnQKl2rvjBZAs6Ur';
    $url="https://graph.facebook.com/".$threadID."?access_token=".$token;
    //will get $x<(cap * 25)
    for($x=0;$x<10;$x++)
    {
        $decoded=json_decode(file_get_contents($url), true);
        Clockwork::info($decoded);
        if($x==0)
        {
            $allmsg=$decoded['messages'];
        }
        else
        {
            $allmsg=$decoded;
        }
        foreach($allmsg['data'] as $eachMessage)
        {
            $info=Array('from_id'=>$eachMessage['from']['id'],
            'from_name'=>$eachMessage['from']['name'],
            'time'=>$eachMessage['created_time'],
            'message'=>$eachMessage['message'],
            'thread_id'=>$threadID);
            $test=Messages::firstOrCreate($info);
        }
        $url=$allmsg['paging']['next'];
        var_dump($url);
    }


    return View::make('test',compact('data'));
}

    public function fbtest()
    {
        $data='yo';
        return View::make('test',compact('data'));
    }
}
