<?php

namespace ttm4135\webapp\controllers;

use Dolondro\GoogleAuthenticator;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\InputSanitizer;
use Sonata\GoogleAuthenticator\GoogleAuthenticator as SonataGoogleAuthenticator;


class AuthController extends Controller {

    protected $secret_key;


    function index() {
        $this->app->request();
        $userid= $_SESSION['userid'];
        $user = User::findById($userid);
        $username = $user->getusername(); 

        $secretFactory = new GoogleAuthenticator\SecretFactory();
        $secret = $secretFactory->create("TTM4135gr04", $username);
        $secret_key = $secret->getSecretKey();
        self::$secret_key = $secret_key;
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
       # $user->setTempAuth($auth_key, $auth_url);
       #TODO add secret key to database

        $this->render('auth.twig', ['url'=>$auth_url]);

    
 
    }


    function auth(){
        $username = $_COOKIE['username'];
        $user = User::findByUser($username);
        $request = $this->app->request;
        $input_sanitizer = new InputSanitizer($request);
        $code = $input_sanitizer->get('code');
        $secret_key = self::$secret_key;
        $googleAuth = new GoogleAuthenticator\GoogleAuthenticator();
        $googleAuth->authenticate($secret_key, $code);

        if($googleAuth){
            $this->app->flash("info", "Authenticator has been successfully set");
            $this->app->redirect("/");
            
        }
        else{
            $this->app->flash("info", "Please set an Authenticator to your account");
            $this->app->redirect("/login/auth");
        }

    }

    
}
