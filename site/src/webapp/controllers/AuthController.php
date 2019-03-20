<?php

namespace ttm4135\webapp\controllers;

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class AuthController extends Controller {


    function index() {
        $this->app->request();
        $userid= $_SESSION['userid'];
        $user = User::findById($userid);
        $username = $user->getusername(); 
    
        $g = new GoogleAuthenticator();
        $gq = new GoogleQrUrl();
        $salt = '7WAO342QFANY6IKBF7L7SWEUU79WL3VMT920VB5NQMW';
        $secret = $username.$salt;

        $qrImageGenerator = $gq->generate($username, $secret);


	    $this->app->render('auth.twig', ['url'=>$qrImageGenerator]);

        }
    
}
