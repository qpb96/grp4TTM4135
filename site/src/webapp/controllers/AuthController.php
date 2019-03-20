<?php

namespace ttm4135\webapp\controllers;

use PHPGangsta_GoogleAuthenticator;
use ttm4135\webapp\Auth;

require_once("../src/webapp/Tools.php");

class AuthController extends Controller {


    function index() {
        $secretFactory = new GoogleAuthenticator\SecretFactory();
        $secret = $secretFactory->create("TTM4135gr18", $username);
        $auth_key = $secret->getSecretKey();
        $qrImageGenerator = new GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $auth_url = $qrImageGenerator->generateUri($secret);
#		$user->setTempAuth($auth_key, $auth_url);
	    $this->render('auth.twig', ['url'=>$auth_url]);

        }
    
}
