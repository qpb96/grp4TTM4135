<?php

namespace ttm4135\webapp;

use ttm4135\webapp\models\User;

class Auth
{
    const SESSION_EXPIRATION_TIME = 10;
    private static $session_expired = null;
    private static $has_session_expired = False;

    function __construct()
    {
    }

    static function checkCredentials($username, $password)
    {
        $user = User::findByUser($username);

        if ($user === null) {
            return false;
        }

        if (password_verify($password, $user->getPassword()))
        {
	  echo 'Pass is valid';
          return true;
        }
	echo 'pass is invalid';
        return false;
    }

    /**
     * Check if is logged in.
     */
    static function check()
    {
        return isset($_SESSION['userid']);
    }

    /**
     * Check if the person is a guest.
     */
    static function guest()
    {
        return self::check() === false;
    }

    /**
     * Get currently logged in user.
     */
    static function user()
    {
        if (self::check()) {
            return User::findById($_SESSION['userid']);         
        }
    }

    /**
     * Is currently logged in user admin?
     */
    static function isAdmin()
    {
        if (self::check()) {
          return self::user()->isAdmin();	// uses this classes user() method to retrieve the user from sql, then call isadmin on that object.
        }

    }

    /** 
     * Does the logged in user have r/w access to user details identified by $tuserid
     */
    static function userAccess($tuserid) 
    {
        if(self::user()->getId() == $tuserid)   //a user can change their account
        {
          return true;
        }
        if (self::isAdmin() )           //admins can change any account
        {
          return true;
        }
        return false;

    }

    //Countdown
    static function updateSessionExpiration() {
        if (isset($_SESSION['timestamp']) && time() - $_SESSION['timestamp'] > self::SESSION_EXPIRATION_TIME) {
            if(self::check()) {
            self::logout($expired=true);
            }
        }
        $_SESSION['timestamp'] = time();
        }

    //Set a session when user logs in
    static function login($user_id) {
        $_SESSION['userid'] = $user_id;
        }
    
    static function register(){
        $_SESSION['registration_counter'] += 1 ;
    }



    static function logout()
    {
        if (self::check()) {
        session_unset();
        session_destroy();	
        session_regenerate_id();
        self::$session_expired = false;
        self::$has_session_expired = true;
    }
}


    static function isSessionExpired() {
	if (self::$has_session_expired) {
	    return true;
	} else {
	    return false;
	}
    }

    static function resetSessionExpired() {
        self::$session_expired = false;
        }
    

}
