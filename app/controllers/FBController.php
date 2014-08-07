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
    $token='CAAKfmL5GXZBgBAOmTEGFUiV3scmbv9ULuyZCzcPBjrYIcBrew9AWvnZC6ZCfQjmqIPamgNbFDxArFIWpspAfffA8ap1CoZCgre2uTyHc5a5UKxNcTW8y0i551luktNXd5AmpJzRh1CHqKAmtqeSS2phHQD8KWmV1L0e0vLbe3SIqYAcoSc1FwRPEvNZB9NQXxnP6Wd6SDRv5JbPjKW0BoE';
    $session = new FacebookSession($token);
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    Clockwork::info($graphObject);
    Clockwork::info('ey');
    Clockwork::info('Message text.');
    $data['fb']=$graphObject;


    $jsonurl  = "https://graph.facebook.com/me?access_token=".$token ;
    $data['fb2'] = json_decode(file_get_contents($jsonurl), true);
    return View::make('test',compact('data'));
    //return(var_dump($graphObject));
}
}
