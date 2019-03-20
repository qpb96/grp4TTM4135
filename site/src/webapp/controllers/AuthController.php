<?php

namespace ttm4135\webapp\controllers;

use Sonata\GoogleAuthenticator;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class AuthController extends Controller {


    function index() {
        $this->app->request();
        $userid= $_SESSION['userid'];
        $user = User::findById($userid);
        $username = $user->getusername(); 
    
        $g = new GoogleAuthenticator();
        $salt = '7WAO342QFANY6IKBF7L7SWEUU79WL3VMT920VB5NQMW';
        $secret = $username.$salt;
       
        $auth_key = $secret->getSecretKey();
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
#		$user->setTempAuth($auth_key, $auth_url);
	    $this->render('auth.twig', ['url'=>$auth_url]);

        }
    
}
