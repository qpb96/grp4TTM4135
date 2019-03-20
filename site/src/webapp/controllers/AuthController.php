<?php

namespace ttm4135\webapp\controllers;

use Dolondro\GoogleAuthenticator;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;


class AuthController extends Controller {


    function index() {
        $this->app->request();
        $userid= $_SESSION['userid'];
        $user = User::findById($userid);
        $username = $user->getusername(); 

        $secretFactory = new GoogleAuthenticator\SecretFactory();
        $secret = $secretFactory->create("TTM4135gr04", $username);
        $auth_key = $secret->getSecretKey();
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
       # $user->setTempAuth($auth_key, $auth_url);

        $this->render('auth.twig', ['url'=>$auth_url]);

    
 
    }


    function auth(){
        $username = $_COOKIE['username'];
        $user = User::findByUser($username);
        $request = $this->app->request;
        $input_handler = new InputHandler($request);
        $code = $input_handler->get('code');
        $auth_key = $user->getAuthKey();


        $googleAuth = new GoogleAuthenticator();
        $googleAuth->authenticate($auth_key, $code);

    }

    
}
