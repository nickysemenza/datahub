<?php namespace App\Http\Controllers;
use Config;
use View;
use App\Models\Threads;
use App\Models\Messages;


class TestController extends Controller
{

    public function stickers()
    {
        //print "hi";

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
        //var_dump($links);
//        print "<table class = 'table table-bordered'>";
        $data['sticker_links']=$links;
//        foreach($links as $k=>$v)
//        {
//            print "<tr>";
//            print "<td><img src='".$k."'></td>";
//            print "<td>".$v."</td>";
//            print "</tr>";
//        }
//
//        print "</table>";


        return View::make('stickers',compact('data'));

    }
}