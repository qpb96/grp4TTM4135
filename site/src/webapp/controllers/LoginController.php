<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;
use ttm4135\webapp\InputValidation;
use ttm4135\webapp\InputSanitizer;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request as r;
header('X-Frame-Options: DENY');

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
        #TODO: check if a user has a secret key, if so, must enter the auth code



        $request = $this->app->request;
        $recaptcha = new ReCaptcha('6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');
        #$resp = $recaptcha->verify($request->get('g-recaptcha'), $request->getIp());
        $input_handler = new InputSanitizer($request);
        $this->validation = new InputValidation();
        $username = $input_handler->get('username');
        $password = $input_handler->get('password');
        
        if(isset($_POST['submit']) && !empty($_POST['submit'])){ 
            // check do we have recaptcha param added to form and submited
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
                //your site secret recaptcha key
                $secret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
                //get verify response data
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                 // check was the response successfully checked by Google
                if($responseData->success){
                // if recaptcha check was success
#               $succMsg = 'Your contact request have submitted successfully.';
                    if($this->validation->isValidUserName($username) == TRUE && $this->validation->isValidPassword($password)){
                        if ( Auth::checkCredentials($username, $password) ) {
                            $user = User::findByUser($username);
                            $uid = $user->getId();
                            if(User::hasAuthKey($uid)){
                            //Set session when user logs in
                            UserController::setCookieUsername($username);
                            $_SESSION['userid'] = $uid;
                            $this->app->flash('info', "Please verify that you are " . $user->getUsername() . ".");
                            $this->app->redirect("\auth");
                            }
                            else{
                                //Set session when user logs in
                                UserController::setCookieUsername($username);

                                Auth::login($user->getId());
                                $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
                                $this->app->redirect('/');
                            }
                        } 
                        else {
                            $this->app->flashNow('error', 'Incorrect username/password combination.');
                            $this->render('login.twig', []);
                        }
                        }
                    else{

                        $this->app->flash("error", "Invalid input in username or password");
                        $this->render('login.twig', []);
                    }
                }else{
                    // if not show the error
                    $this->app->flash("info", "Robot verification failed");
                    $this->app->redirect('/login');
                }
            }else{
                // if recaptcha is not checked
                $this->app->flash("info", "Please mark the checkbox");
                $this->app->redirect('/login');
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
