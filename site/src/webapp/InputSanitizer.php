<?php

namespace ttm4135\webapp;

class InputSanitizer {

    private $request;

    public function __construct($request) {
	$this->request = $request;
    }
    
    function sanitizeeInput($input) {
    	$input = trim($input); //strip white
    	$input = stripslashes($input); //strip \
    	$input = strip_tags($input); //Strip html tags
        $input = htmlspecialchars($input); //Convert
        $input = escapeshellcmd($input); // escapeshellcmd() escapes any characters in a string that might be used to trick a shell command into executing arbitrary commands.
 #       $input = escapeshellarg($input);  //escapeshellarg() adds single quotes around a string and quotes/escapes any existing single quotes
                                          // allowing you to pass a string directly to a shell function and having it be treated as a single safe argument
    	return $input;
    }

    function get($req) {
	$raw_value = $this->request->post($req);
	$cleaned_value = $this->sanitizeeInput($raw_value);
	return $cleaned_value;
    }

}

?>