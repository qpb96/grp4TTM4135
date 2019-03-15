<?php 
    namespace ttm4135\webapp\controllers;

use Slim\Middleware\Flash;


    class InputValidation {
        function validUserName($username)
        {
            if($username == null){
                return FALSE; 
            }
            else{
                return TRUE;
            }


        }
    }