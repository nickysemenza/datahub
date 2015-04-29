<?php namespace App\Http\Controllers;
use DateTime;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;
use Config;
use View;
use App\Models\Threads;
use App\Models\Messages;

FacebookSession::setDefaultApplication(Config::get('keys.fb_appid'), Config::get('keys.fb_secret'));

class FBChatController extends Controller {
/**
 * downloads all the possible threads
 */
public function getFBThreads()
{
    $token=Config::get('keys.fb_token');
    $data=0;
    $url="https://graph.facebook.com/me/threads?limit=1&access_token=".$token;
    for($x=0;$x<30;$x++)
    {
        echo($url);
        $decoded=json_decode(file_get_contents($url), true);
        foreach($decoded['data'] as $eachThread)
        {
            $participants_ids=array();
            $participants_names=array();
            $this->er($eachThread);

            foreach($eachThread['participants']['data'] as $participants)
            {
                array_push($participants_ids,$participants['id']);
                array_push($participants_names,$participants['name']);
            }
            $info = Array('thread_id'=>$eachThread['id'],
                'participants_ids'=>implode(",", $participants_ids),
                'participants_names'=>preg_replace('/[^a-z0-9_, ]/i', '', utf8_encode(implode(",", $participants_names))),
            );
            var_dump($info);
            $test=Threads::firstOrCreate($info);
            $test->message_count=$eachThread['message_count'];
            var_dump($eachThread['message_count']);
            $test->save();
        }
        try{
            $url=$decoded['paging']['next'];
        }
         catch(ErrorException $e)
        {
            error_log("Error:");
            break;
        }
    }
}

/**
 * gets all the messages from a thread
 * @param $thread_id
 * @return
 */
public function getFBMessagesFromThread($thread_id)
{

    print("dropping messages");

    Messages::where('thread_id',$thread_id)->delete();


    $data=$thread_id;
    $token=Config::get('keys.fb_token');
    $url="https://graph.facebook.com/".$thread_id."?access_token=".$token;
    echo$url;
    //will get $x<(cap * 25)
    $cap=1000;
    $threadLookup=Threads::find($thread_id);
    if($threadLookup!=null)
    {
        $cap=($threadLookup->message_count+30)/25;
    }
    for($x=0;$x<$cap;$x++)
    {
        $this->err_echo("processing ".($x*25)." to ".(($x+1)*25)." of ".(($cap-1)*25)." (".(100*($x*25))/(($cap-1)*25)."%)");
        $decoded=json_decode(file_get_contents($url), true);
        //echo(file_get_contents($url));
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
            $datablob="";
            //error_log(print_r($eachMessage,true));
            if(isset($eachMessage['shares']))
            {
                $datablob=json_encode($eachMessage['shares']);
            }

            $tagData="";
            //error_log(print_r($eachMessage,true));
            if(isset($eachMessage['tags']))
            {
                $tagData=json_encode($eachMessage['tags']);
                //echo $tagData;
            }

            $attachmentData="";
            //error_log(print_r($eachMessage,true));
            if(isset($eachMessage['attachments']))
            {
                $attachmentData=json_encode($eachMessage['attachments']);
                //var_dump($attachmentData);
                //echo $tagData;
            }




            $d = DateTime::createFromFormat(DateTime::ISO8601, $eachMessage['created_time']);

            $messageText=$eachMessage['message'];
            $info=Array('from_id'=>$eachMessage['from']['id'],
            'from_name'=>$eachMessage['from']['name'],
            'time_string'=>$eachMessage['created_time'],
            'time_stamp'=>strtotime($eachMessage['created_time']),
            'time'=>$d->format('Y-m-d H:i:s'),
            'message'=>$messageText,
            'data_shares'=>$datablob,
            'data_attachments'=>$attachmentData,
            'tags'=>$tagData,
            'thread_id'=>$thread_id);
            $test=Messages::firstOrCreate($info);
        }
        try{
            $url=$allmsg['paging']['next'];
            print($url);
        }
        catch(ErrorException $e)
        {
            error_log("Error:");
            break;
        }
        error_log($url);
    }
    return View::make('test',compact('data'));
}
    public function extendToken($token)
    {
        $session = new FacebookSession($token);
        $extendedToken = (new FacebookRequest(
            $session, 'GET', '/oauth/access_token', array(
                'grant_type' => 'fb_exchange_token',
                'client_id' => Config::get('keys.fb_appid'),
                'client_secret' => Config::get('keys.fb_secret'),
                'fb_exchange_token' => $session->getToken()
            )
        ))->execute()->getGraphObject(GraphUser::className());
        $token = $extendedToken->getProperty('access_token');
        var_dump($token);
    }
    public function showThreads()
    {
        $threadsArray=array();
        $threads = Threads::orderBy('message_count','DESC')->get();
        foreach($threads as $eachThread)
        {
            array_push($threadsArray,array(
                'message_count'=>$eachThread['message_count'],
                'downloaded_message_count'=>$eachThread['downloaded_message_count'],
                'thread_id'=>$eachThread['thread_id'],
                'people'=>$eachThread['participants_names']
                //'downloaded_message_count'=>$this->getNumDownloadedMessages($eachThread['thread_id'])
            ));
        }
        $data['threads']=$threadsArray;

        return View::make('threads',compact('data'));
    }
    public function updateNumDownloadedMessages($thread_id)
    {

        $count = Messages::where('thread_id', '=', $thread_id)->count();
        $threadLookup=Threads::find($thread_id);
        $threadLookup->downloaded_message_count=$count;
        $threadLookup->save();
        return $count;


    }



    public function showThreadsJSON()
    {
        $threadsArray=array();
        //$threads = Threads::all()->-take(10);
        $threads=Threads::orderBy('message_count', 'DESC')->take(20)->get();
        foreach($threads as $eachThread)
        {
            array_push($threadsArray,array('message_count'=>$eachThread['message_count'],'thread_id'=>$eachThread['thread_id'],'people'=>$eachThread['participants_names']));
        }
        echo(json_encode($threadsArray));
    }
    public function getThreadWordCloudJSON($thread_id)
    {
        $messagesArray=array();
        $messages = Messages::where('thread_id', '=', $thread_id)->take(4000)->get();
        foreach($messages as $eachMessage)
        {
            $msgs=explode(" ", $eachMessage['message']);
            $messagesArray=array_merge($messagesArray,$msgs);
        }
        $result = array_count_values($messagesArray);
        arsort($result);
        echo json_encode($result);
    }
    public function getSSLPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function er($data)
    {
        ob_start();
        print_r($data);
        $contents = ob_get_contents();
        ob_end_clean();
        error_log($contents);
    }
    public function updateMessageCount()
    {
        echo("Updating Message Count...");
        $threads = Threads::orderBy('message_count','DESC')->get();
        foreach($threads as $eachThread)
        {
            $thread_id = $eachThread['thread_id'];
            $this->updateNumDownloadedMessages($thread_id);
        }
        echo("Done!");

    }
    public function err_echo($data)
    {
        error_log($data);
        echo($data."\n");
    }
}
