<?php
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;
FacebookSession::setDefaultApplication(Config::get('keys.fb_appid'), Config::get('keys.fb_secret'));

class FBFriendGraphController extends BaseController {
    public function getFBFriends()
    {
        //$token=Config::get('keys.fb_secret');
        $token='CAAKfmL5GXZBgBAFbJEIocqq0PbYqOZADqIXmAZBNUCrQ878K58xneNRMDk1Sc7WsIbMAdIxSpRsPWJEKPtb3aw3cYBIUYpiAz7rZBGag6p6GA1RDHD7gICZBFvanPZAfAjlPB6UF9TSco1ZCoV9zkN8Dyvd03vAvbDzDq7BXxkF7swXFNBzHEXQ39vgn4KmIbH0KA0Ji3TCsXZCUxe2OJuS4';
//        $session = new FacebookSession($token);
//
//        /* PHP SDK v4.0.0 */
//        /* make the API call */
//        $request = new FacebookRequest(
//            $session,
//            'GET',
//            '/me/friends'
//        );
//        $response = $request->execute();
//        $graphObject = $response->getGraphObject();
//        $this->er($graphObject);
        /* handle the result */
        //$token=Config::get('keys.fb_secret');
        $url="https://graph.facebook.com/v1.0/me/friends?access_token=".$token;
        for($x=0;$x<30;$x++)
        {
            $decoded=json_decode(file_get_contents($url), true);
            $this->er($decoded);
            foreach($decoded['data'] as $eachPage)
            {
                //$this->er($eachPage);

//                foreach($eachPage['participants']['data'] as $participants)
//                {
//                    array_push($participants_ids,$participants['id']);
//                    array_push($participants_names,$participants['name']);
//                }
//                $info = Array('thread_id'=>$eachPage['id'],
//                    'participants_ids'=>implode(",", $participants_ids),
//                    'participants_names'=>preg_replace('/[^a-z0-9_, ]/i', '', utf8_encode(implode(",", $participants_names))),
//                );
//                $test=Threads::firstOrCreate($info);
//                $test->message_count=$eachPage['message_count'];
//                $test->save();
            }
            $url=$decoded['paging']['next'];
        }
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