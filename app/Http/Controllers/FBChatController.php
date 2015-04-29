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
    public function test()
    {
        //$data=array();
        //$data['aa']=$this->updateNumDownloadedMessages('t_msg.a59d3107762c9a3fc773b90567d218ed51');
        return View::make('layout',compact('data'));
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
    public  function getThread($thread_id,$limit=50)
    {
        date_default_timezone_set("America/Los_Angeles");
        $messagesArray = Messages::where('thread_id', '=', $thread_id)->take($limit)->get();
        $data['messages']=$messagesArray;
        $data['thread_id']=$thread_id;

        $threadLookup=Threads::find($thread_id);
        if($threadLookup!=null)
        {
            $data['names']=$threadLookup->participants_names;
        }
        return View::make('thread',compact('data'));
    }
    public function getSpecialThread($thread_id,$query="",$dateFormat="Y-z")
    {
        if($query=="all")
        {
            $query="";
        }
        if($thread_id=="all")
        {
            $thread_id="";
        }

        date_default_timezone_set('America/New_York');
        $messages = Messages::where('thread_id', 'like', '%'.$thread_id.'%')->where('message','like','%'.$query.'%')->get();
        $frequencies=array();
        $dates=array();
        $names=array();
        foreach($messages as $each)
        {
            $formattedDate=date($dateFormat, strtotime($each->time_string));
            if(!in_array($formattedDate,$dates))
            {
                array_push($dates,$formattedDate);
            }
            if(!in_array($each->from_name,$names))
            {
                array_push($names,$each->from_name);
            }

            if(isset($frequencies[$each->from_name][$formattedDate]))
            {
                $frequencies[$each->from_name][$formattedDate]++;
            }
            else
            {
                $frequencies[$each->from_name][$formattedDate]=1;
            }
        }

        sort($names);
        $headers=$names;
        array_unshift($headers,'dates');
        $chartData=array($headers);
        sort($dates);

        foreach($dates as $eachDay)
        {
            $temp=array($eachDay);
            foreach($names as $eachName)
            {
                if(isset($frequencies[$eachName][$eachDay]))
                {
                    array_push($temp,$frequencies[$eachName][$eachDay]);
                }
                else
                {
                    array_push($temp,0);
                }
            }
            array_push($chartData,$temp);
        }
        $data['chartdata']=json_encode($chartData);
        $data['chartname']='plotting occurance of ['.$query.'] vs time';
        $data['thread_id']=$thread_id;
        return View::make('gchart',compact('data'));
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
