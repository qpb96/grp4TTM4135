<?php 
    namespace ttm4135\webapp\controllers;

use Slim\Middleware\Flash;


    class InputValidation {
        function validUserName($username)
        {
            if(username == null){
                $this->app->flash("Username field can't be empty");
                return FALSE; 
            }
            else
                return TRUE;


        }
    }