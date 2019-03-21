<?php

namespace ttm4135\webapp\controllers;

use Dolondro\GoogleAuthenticator;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\InputSanitizer;



class AuthController extends Controller 
{
    protected $uid;


    function index_login(){
        if(Auth::check()){   
            $this::$uid = $_SESSION['userid'];
            Auth::logout();
            Auth::resetSessionExpired();
            $this->render('login_auth.twig', []);
        }
        else{
            $this->app->redirect("/");
        }
    }

    function verify_login(){
        $request = $this->app->request;
        $input_sanitizer = new InputSanitizer($request);
        $code = $input_sanitizer->get('code');
        $uid = $_SESSION['userid'];
        $secret_key = User::getOfficialAuthKey(self::$uid);
        $googleAuth = new GoogleAuthenticator\GoogleAuthenticator();
        $is_valid_auth = $googleAuth->authenticate($secret_key, $code);
        if($is_valid_auth){
            $this->app->flash("info", "Successful Verification");
            $this->app->redirect("/");
        }
        else
            $this->app->flash("info", "Wrong code");
            $this->app->redirect("/auth");
            $this->render('base.twig', []);
        }
    


        
    


    function index() {
        $this->app->request();
        $userid= $_SESSION['userid'];
        $user = User::findById($userid);
        $username = $user->getusername(); 

        $secretFactory = new GoogleAuthenticator\SecretFactory();
        $secret = $secretFactory->create("TTM4135gr04", $username);
        $secret_key = $secret->getSecretKey();
        $_SESSION['secret_key'] = $secret_key;
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
        echo $_SESSION['secret_key'];
       # $user->setTempAuth($auth_key, $auth_url);
       #TODO add secret key to database

        $this->render('auth.twig', ['url'=>$auth_url]);
 
    }

    function auth(){
        #$username = $_SESSION['username'];
        #$user = User::findByUser($username);
        echo $_SESSION['userid'];
        $uid = $_SESSION['userid'];
        $request = $this->app->request;
        $input_sanitizer = new InputSanitizer($request);
        $code = $input_sanitizer->get('code');
        $secret_key = $_SESSION['secret_key'];
        $googleAuth = new GoogleAuthenticator\GoogleAuthenticator();
        $is_valid_auth = $googleAuth->authenticate($secret_key, $code);

        if(!$is_valid_auth){
            $this->app->flash("info", "Invalid code");
            $this->app->redirect("/login/auth");
            
        }
        else{
            User::insertAuthKey($secret_key, $uid);
            unset($_SESSION['secret_key']);
            $this->app->flash("info", "Authenticator has been successfully set");
            $this->app->redirect("/");
        }

    }
}

    

