<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;


class HomeController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        $this->hasSessionExpired();
        
        if (Auth::check()) {
            $user = Auth::user();
            $this->render('base.twig', ['user'=>$user]);
        } 
        else {
            $this->render('base.twig',[]);
        }
    }

    function help()
    {
     $this->render('help.twig', []);
    }

       
    



}
