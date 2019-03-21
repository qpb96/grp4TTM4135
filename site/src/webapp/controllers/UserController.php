<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\InputValidation;
use ttm4135\webapp\InputSanitizer;



class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->hasSessionExpired();
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    static function setCookieUsername($username){
    	parent::setCookie("username", $username, "/login");
    }

    function create()
    {
        $request = $this->app->request;
        $sanitizer = new InputSanitizer($request);
        $username = $sanitizer->get('username');
        $password = $sanitizer->get('password');
        $email = $sanitizer->get('email');
        $bio = $sanitizer->get('bio');
        $validation = new InputValidation();

        if($validation->isValidEmail($email) && $validation->isValidBio($bio)
            && $validation->isValidUserName($username) && $validation->passwordRequirement($password))
            {
                $user = User::makeEmpty();
                $user->setUsername($username);
                $password_hashed =  password_hash($password, PASSWORD_DEFAULT);
                $user->setPassword($password_hashed);
                $user->setEmail($email);
                $user->setBio($bio);
                $user->save();

                $user = User::findByUser($username);
                Auth::login($user->getId());
                $this->app->flash('info', 'Thanks for creating a user. Please add a 2fa to your account.');
                $this->app->redirect('/login/auth');
            }
            else{

                if(!$validation->passwordRequirement($password)){
                    $this->app->flash('error', 'Password must be a minimum of 8 characters,
                    \n contain at least 1 number,
                    \n	contain at least one uppercase character,
                    \n	and contain at least one lowercase character.');

                } else if(!$validation->isValidUserName($username)){
                    $this->app->flash('error', 'Name is already taken or contain over over 20 characters');

                } else{
                    $this->app->flash('error', 'Invalid input field.');

                }
                $this->app->redirect('/register');
            }


    }

    function show($tuserid)
    {
        if(Auth::userAccess($tuserid) )
        {
          $user = User::findById($tuserid);
          $this->render('showuser.twig', [
            'user' => $user
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }


    function edit($tuserid)
    {

        $user = User::findById($tuserid);

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        } elseif (Auth::userAccess($tuserid)) {


            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
	          $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $email = $request->post('email');
            $bio = $request->post('bio');


            $isAdmin = ($request->post('isAdmin') != null);


            $user->setUsername($username);
            $user->setPassword($password_hashed);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $user = User::findById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

}
