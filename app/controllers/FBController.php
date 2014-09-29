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
    $token=Config::get('keys.fb_secret');
    $data=0;
    $url="https://graph.facebook.com/me/threads?access_token=".$token;
    for($x=0;$x<30;$x++)
    {
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
    $token=Config::get('keys.fb_secret');
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
        $threads = Threads::orderBy('message_count','DESC')->get();
        foreach($threads as $eachThread)
        {
            array_push($threadsArray,array('message_count'=>$eachThread['message_count'],'thread_id'=>$eachThread['thread_id'],'people'=>$eachThread['participants_names']));
        }
        $data['threads']=$threadsArray;

        return View::make('threads',compact('data'));
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

        date_default_timezone_set('America/Los_Angeles');
        $messages = Messages::where('thread_id', 'like', '%'.$thread_id.'%')->where('message','like','%'.$query.'%')->get();
        $frequencies=array();
        $dates=array();
        $names=array();
        foreach($messages as $each)
        {
            $formattedDate=date($dateFormat, strtotime($each->time));
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

//        $sortbyname=array(array('day','meggin','nicky'));
//        foreach($frequencies as $person_name =>$value1)
//        {
//            echo($person_name);
//            foreach($value1 as $day_name=>$count)
//            {
//                echo "Name: $person_name; Day: $day_name; Count: $count<br>";
//                array_push($sortbyname,array($day_name,$person_name,$count));
//            }
//        }

//        $result = array_count_values($messagesArray);
//        ksort($result);
//        $test=array(array('herp','derp'));
//        foreach ($result as $key => $value) {
//            echo "Key: $key; Value: $value<br />\n";
//            array_push($test,array($key,$value));
//        }
//        echo "<pre>".var_dump($frequencies)."</pre>";



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
}
