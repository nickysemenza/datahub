<?php namespace App\Http\Controllers;
use Config;
use View;
use App\Models\Threads;
use App\Models\Messages;


class FBThreadDetailController extends Controller {
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

    public function stickers()
    {

        $links=array();
//        $test = Messages::where("thread_id","t_mid.1411092543102:5cb624c43e1f3ada42")->where('data', 'like', '%dragon%')->get();
        $test = Messages::where("thread_id","t_mid.1418244847157:211f247516df552190")->where('data_shares', 'like', '%dragon%')->get();

        $attachments=array();
        foreach($test as $eachMessage)
        {
            $data_decoded = json_decode($eachMessage->data_shares,true);
            $link = $data_decoded['data'][0]['link'];
            if(array_key_exists($link,$links))
                $links[$link]+=1;
            else
                $links[$link]=1;


        }
        arsort($links);
        $data['sticker_links']=$links;
        return View::make('stickers',compact('data'));

    }

}