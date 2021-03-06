<?php

namespace ttm4135\webapp\controllers;

use Dolondro\GoogleAuthenticator;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\InputSanitizer;



class AuthController extends Controller 
{



    function index_login(){
        if(Auth::check()){ 
            $userid = $_SESSION['userid'];
            $user = User::findById($userid);  
            $username = $user->getUsername();
            
            Auth::logout();
            Auth::resetSessionExpired();
            $_SESSION['temp_uid'] = $userid;
            $this->app->render('login_auth.twig',['username' => $username]);

        }
        else{
            $this->app->redirect("/");
        }
    }

    function verify_login(){
        $request = $this->app->request;
        $input_handler = new InputSanitizer($request);
        $username = $input_handler->get('username');
        $code = $input_handler->get('code');
        $user = User::findByUser($username);
        Auth::login($user->getId());

        $secret_key = User::getOfficialAuthKey($user->getId());
        $googleAuth = new GoogleAuthenticator\GoogleAuthenticator();
        $is_valid_auth = $googleAuth->authenticate($secret_key, $code);
        if($is_valid_auth){
            $this->app->flash("info", "Successful Verification");
            $this->app->redirect("/");
        }
        else{
            $this->app->flash("error", "Wrong code");
            $this->app->redirect("/auth");
        }

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

       # $user->setTempAuth($auth_key, $auth_url);
       #TODO add secret key to database

        $this->render('auth.twig', ['url'=>$auth_url, 'user' =>$user]);
 
    }

    function auth(){
        #$username = $_SESSION['username'];
        #$user = User::findByUser($username);

        $uid = $_SESSION['userid'];
        $request = $this->app->request;
        $input_sanitizer = new InputSanitizer($request);
        $code = $input_sanitizer->get('code');
        $secret_key = $_SESSION['secret_key'];
        $googleAuth = new GoogleAuthenticator\GoogleAuthenticator();
        $is_valid_auth = $googleAuth->authenticate($secret_key, $code);

        if(!$is_valid_auth){
            $this->app->flash("error", "Invalid code");
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

    

