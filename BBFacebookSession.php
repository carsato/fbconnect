<?php

//namespace BB;
require dirname(__DIR__).'/fbconnect/facebook-php-sdk-v4/autoload.php';


use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\Entities\AccessToken;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\GraphUser;
use Facebook\GraphLocation;

Class BBFacebookSession extends FacebookSession{
  private $conf;
  private $fbuid;
  private $session;
  private $info;

  function __construct($token, $config){
    parent::__construct($token);
    $this->conf = $config;
  }

  function getUser(){
    return $this->getUserId();
  }

  function getUserId(){
    $this->fbuid;
    $conf = $this->conf;

//    $session = false;
    // try to get a session from saved token
    try {
      if(!$this->fbuid) {
        FacebookSession::setDefaultApplication($conf['app_id'], $conf['secret_api_key']);
        $_bb_fb_token = $_SESSION['_bb_fb_token'];
        $this->session = new FacebookSession($_bb_fb_token);
        $this->fbuid = $this->session->getUserId();
      }
    }
    catch(FacebookApiException $e){
    }
    catch(\Exception $e) {
    }


    if(!$this->fbuid){
      $this->session = $this->bb_fb_get_session_from_js();
      if($this->session) {
        $this->fbuid = $this->session->getUserId();
      }
    }

    return $this->fbuid;
  }

  function api(){
    $this->info = false;

    $request = new FacebookRequest($this->session, 'GET', '/me');
    $response = $request->execute();
    $this->info = $response->getGraphObject()->asArray();
    return $this->info;
  }

  function bb_fb_get_session_from_js(){
    try {
      $helper = new FacebookJavaScriptLoginHelper();
      $this->session = $helper->getSession();
    } catch(FacebookRequestException $ex) {
      // When Facebook returns an error
    } catch(\Exception $ex) {
      // When validation fails or other local issues
    }
    return $this->session;
  }
}