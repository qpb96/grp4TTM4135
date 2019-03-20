<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;
use ttm4135\webapp\InputValidation;
use ttm4135\webapp\InputSanitizer;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request as r;


class LoginController extends Controller
{
    private $validation;

    function __construct()
    {
        parent::__construct();
        
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
		if (isset($_COOKIE["username"])){
			$username = $_COOKIE["username"];
		} else {
			$username = "";
		}
            $this->render('login.twig', ['title'=>"Login", 'username'=>$username]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $recaptcha = new ReCaptcha('6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');
        $resp = $recaptcha->verify($request->get('g-recaptcha'), $request->getIp());
        
  

        if (!$resp->isSuccess()) {
            $this->app->flash("info", "Prove us that you are not a bot");
            $this->app->redirect("/login");

        }else{
            $input_handler = new InputSanitizer($request);
            $this->validation = new InputValidation();
            
            $username = $input_handler->get('username');
            $password = $input_handler->get('password');
    
            if($this->validation->isValidUserName($username) == TRUE && $this->validation->isValidPassword($password)){
                if ( Auth::checkCredentials($username, $password) ) {
                    $user = User::findByUser($username);
                    //Set session when user logs in
                    UserController::setCookieUsername($username);	
                    Auth::login($user->getId());
                    $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
                    $this->app->redirect('/');
                } else {
                    $this->app->flashNow('error', 'Incorrect username/password combination.');
                    $this->render('login.twig', []);
                }
            }
            else{
                print($username);
                print($password);
                $this->app->flash("error", "Invalid input in username or password");
                $this->render('login.twig', []);            
            }
        }
          

    }

    function logout()
    {   
        Auth::logout();
        $this->app->flashNow('info', 'Logged out successfully!!');
        Auth::resetSessionExpired();
        $this->render('base.twig', []);
        return;
       
    }

    function expired() {
        Auth::resetSessionExpired();
        $this->app->flash('info', 'Your session has expired. Please log in again.');
        $this->app->redirect('/login');
        }
}
