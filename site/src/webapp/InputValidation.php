<?php 
    namespace ttm4135\webapp;




    class InputValidation {
        function isValidUserName($username)
        {
            if($username == null || strlen($username)> 20 ){
                return FALSE; 
            }
            else{
                return TRUE;
            }

        }

        function isValidPassword($password){
            
            if($password == null || strlen($password)>20){
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
//NOT NULL
//Max 20 characters
//Valid email format, The FILTER_VALIDATE_EMAIL filter validates an e-mail address.
        function isValidEmail($email){
            if(strlen($email) < 20 && $email != NULL && filter_var($email, FILTER_VALIDATE_EMAIL)){
                return TRUE;
            }
            return FALSE;

        }

//Max 200 characters
        function isValidBio($bio){
            if(strlen($bio) < 200 && $bio !=NULL ){
                return TRUE;
            }
            return FALSE;
        }





    }
