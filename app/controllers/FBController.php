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
public function getFBMessagesFromThread($thread_id)
{
    $data=$thread_id;
    $token='CAAKfmL5GXZBgBAEmLXoyPIYnYXuGx3iWbfojWv6bHlMsX0j46qDjKZCJKPTBIFy5g5aIQy8HnVuaxJ0GE8aRn192bZB79g2NFd2VxwgBMFXTSMGsjxWqtc6gMuPJoDJl89OylFpQUlMRLXlUorKP79dZCeRDLUDqHFiLwoDaoj5LZAMzOOOKr5cHfHweWQ3kZD';
    $url="https://graph.facebook.com/".$thread_id."?access_token=".$token;
    //will get $x<(cap * 25)
    $cap=1000;
    $threadLookup=Threads::find($thread_id);
    if($threadLookup!=null)
    {
        $cap=($threadLookup->message_count+30)/25;
    }
    for($x=0;$x<$cap;$x++)
    {
        error_log("processing ".($x*25)." to ".(($x+1)*25)." of ".(($cap-1)*25)." (".(100*($x*25))/(($cap-1)*25)."%)");
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
            'thread_id'=>$thread_id);
            $test=Messages::firstOrCreate($info);
        }
        $url=$allmsg['paging']['next'];
        var_dump($url);
    }
    return View::make('test',compact('data'));
}
    public function extendToken($token)
    {
        $session = new FacebookSession($token);
        $extendedToken = (new FacebookRequest(
            $session, 'GET', '/oauth/access_token',array(
                'grant_type'=>'fb_exchange_token',
                'client_id'=> Config::get('keys.fb_appid'),
                'client_secret'=>Config::get('keys.fb_secret'),
                'fb_exchange_token'=>$session->getToken()
            )
        ))->execute()->getGraphObject(GraphUser::className());
        $token=$extendedToken->getProperty('access_token');
        Clockwork::info(array("token"=>$token));
        var_dump($token);
    }
    public function fbtest()
    {
        //$this->extendToken('CAAKfmL5GXZBgBAMunZB7XLhJEA22xbu7ittBhgZAgjaTsQvY6ZArncgBdj6AJjB93JuKek2jkrsULKtyaYYlhyzILKFDSZCop323h5ZC1O7DTisbpKiMkFoe19ZCz5h9mZB3ev6oacrE8yxfxeuZAKhPI9AVYs6lzwXCpPWNaKYEWSE8akI4ze3ZA54vGTmMrsHRHfqmPXVHg1MRRRioMXuDoz');
        $data='yo';
        return View::make('test',compact('data'));
    }
    public function showThreads()
    {
        $threadsArray=array();
        $threads = Threads::all();
        foreach($threads as $eachThread)
        {
            array_push($threadsArray,array('message_count'=>$eachThread['message_count'],'thread_id'=>$eachThread['thread_id'],'people'=>$eachThread['participants_names']));
        }
        $data['threads']=$threadsArray;

        return View::make('threads',compact('data'));
    }
    public  function getThread($thread_id,$limit=50)
    {

        $messagesArray=array();
        $messagesArray = Messages::where('thread_id', '=', $thread_id)->take($limit)->get();
        $data['messages']=$messagesArray;
        $data['thread_id']=$thread_id;
        return View::make('thread',compact('data'));
    }
}
