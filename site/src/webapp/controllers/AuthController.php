<?php

namespace ttm4135\webapp\controllers;

use PHPGangsta_GoogleAuthenticator;
use ttm4135\webapp\Auth;

require 'vendor/autoload.php';

class AuthController extends Controller {


    function index() {
        $this->app->request();
        $secretFactory = new PHPGangsta_GoogleAuthenticator();
        $secret = $secretFactory->create("TTM4135gr18", "$asd");
        $auth_key = $secret->getSecretKey();
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
#		$user->setTempAuth($auth_key, $auth_url);
	    $this->render('auth.twig', ['url'=>$auth_url]);

        }
    
}
