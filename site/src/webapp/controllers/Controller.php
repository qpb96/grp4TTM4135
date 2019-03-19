<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
class Controller
{
    protected $app;
    const COOKIE_LIFESPAN = 86400*30; //86400 = 1 day

    function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
    }

    function render($template, $variables = [])
    {     
      if (! Auth::guest()) {
            $user = Auth::user();
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $user->isAdmin();
            $variables['loggedInUsername'] = $user->getUsername();
            $variables['loggedInID'] = $user->getId();
        }
        print $this->app->render($template, $variables);
    }

    static function setCookie($name, $value, $path) {
        $expiration = time() + self::COOKIE_LIFESPAN;
        $domain = "ttm4135.item.ntnu.no";
        $secure = true;
        $httponly = true;
        setcookie($name, $value, $expiration, $path, $domain, $secure, $httponly);
    }
}

#Just testing things
