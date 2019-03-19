<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\InputValidation;
use ttm4135\webapp\InputSanitizer;

class AdminController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        if (Auth::isAdmin()) {
            $users = User::all();
            $this->render('users.twig', ['users' => $users]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()
    {
        if (Auth::isAdmin()) {
          $user = User::makeEmpty();
          $this->render('showuser.twig', [
            'user' => $user
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function show($tuserid)   
    {
        if(Auth::userAccess($tuserid) && Auth::isAdmin())
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
        } elseif (Auth::userAccess($tuserid) && Auth::isAdmin()) {

            $request = $this->app->request;
            $sanitizer = new InputSanitizer($request);
            $username = $sanitizer->get('username');
            $password = $sanitizer->get('password');
            $email = $sanitizer->get('email');
            $bio = $sanitizer->get('bio');
            $validation = new InputValidation();


            $isAdmin = ($request->post('isAdmin') != null);
            if ($validation->isValidEmail($email) && $validation->isValidBio($bio)
            && $validation->isValidUserName($username) && $validation->isValidPassword($password)){
                
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'User successfully edited.');

            $user = User::findById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);
            }
            else{
                $this->app->flash('error', 'Invalid input field(s).');
                $this->app->redirect('/admin');
            }
            


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function delete($tuserid)
    {
        if(Auth::userAccess($tuserid) && Auth::isAdmin())
        {
            $user = User::findById($tuserid);
            $user->delete();
            $this->app->flash('info', 'User ' . $user->getUsername() . '  with id ' . $tuserid . ' has been deleted.');
            $this->app->redirect('/admin');
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    
    function deleteMultiple()
    {
      if(Auth::isAdmin()){
          $request = $this->app->request;
          $userlist = $request->post('userlist'); 
          $deleted = [];

          if($userlist == NULL){
              $this->app->flash('info','No user to be deleted.');
          } else {
               foreach( $userlist as $duserid)
               {
                    $user = User::findById($duserid);
                    if(  $user->delete() == 1) { //1 row affect by delete, as expect..
                      $deleted[] = $user->getId();
                    }
               }
               $this->app->flash('info', 'Users with IDs  ' . implode(',',$deleted) . ' have been deleted.');
          }

          $this->app->redirect('/admin');
      } else {
          $username = Auth::user()->getUserName();
          $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
          $this->app->redirect('/');
      }
    }

    function newuser()
    { 

        $user = User::makeEmpty();

        if (Auth::isAdmin()) {


            $request = $this->app->request;
            $isAdmin = ($request->post('isAdmin') != null);
            $this->validation = new InputValidation();
            $sanitizer = new InputSanitizer($request);

            $username = $sanitizer->get('username');
            $password = $sanitizer->get('password');
            $email = $sanitizer->get('email');
            $bio = $sanitizer->get('bio');
            $validation = new InputValidation();

            if($validation->isValidEmail($email) && $validation->isValidBio($bio)
                && $validation->isValidUserName($username) && $validation->isValidPassword($password))
                {
                    $user = User::makeEmpty();
                    $user->setUsername($username);
                    $password_hashed =  password_hash($password, PASSWORD_DEFAULT);
                    $user->setPassword($password_hashed);
                    $user->setEmail($email);
                    $user->setBio($bio);
                    $user->save();
    
<<<<<<< HEAD
                    $this->app->flash('info', 'User succesfully created');
                    $this->app->redirect('/admin');
                }
                else{
    
                    $this->app->flash('error', 'Invalid input field(s).');
                    $this->app->redirect('/admin/create');
                }


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }
    






}
