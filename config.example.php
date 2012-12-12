<?php
$HOSTURL=''; //Set the host url here, e.g. http://opal.test.me/
$IMPRESSUM=''; //Set the url to your impressum, if necessary.
$MINFILES=2; //Minimum / standard number of structures
$MAXFILES=3; //Number of the Maximum Structures allowed
$TIMEOUT=10; //Number of seconds which the program is allowed to run
$TIMEOUTTXT='Your program maybe timed out (>'.$TIMEOUT.'s)'; //text shown, if program did timeout
$SESSIONTIMEOUT=600; //time in seconds until the session expire
$EXAMPLECODE_IMPL = 'DEF hello == "Hello World!"';
$EXAMPLECODE_SIGN = 'FUN hello : denotation';
$EXAMPLECODE_CMD = 'hello';
$VERSION = 'v0.2'; //the current version
$ADVERTCOMMENT = '-- created with WebOpal '.$VERSION.' (https://github.com/gehaxelt/PHP-Webopal) on Server '.$HOSTURL; //comment, which appears in files
$TMPDIR = 'tmp'; //temp-directory
$RUNMAX = 10;
$DEBUGMODE = false; // Enable debug mode? Will show var_dump of $_SESSION in debug.php

// For Bugreport PHP >= 5.3.2 with cURL extension needed
$BUGREPORT = false;	//Enable, if you want the users to be able to report Bugs/Ideas.

//Please fill out the following in order to get the bugreport to work
$ISSUEREPO='';			//Repository where the Bugs should be reported, e.g. PHP-Webopal
$ISSUEUSER= '';		//User which owns the repository, e.g. gehaxelt
$GITHUBUSER='';		//User that makes the reports
$GITHUBPW='';			//Password of that user
$PUBLICKEY = '';		//Public Key of reCaptcha (can be obtained here http://recaptcha.net)
$PRIVATEKEY = '';		//Private Key of reCaptcha (can be obtained here http://recaptcha.net)
// Starting here not required for Bugreport

//DONT CHANGE BELOW HERE
if($ISSUEREPO==''||$ISSUEUSER== ''||$GITHUBUSER==''||$GITHUBPW==''||$PUBLICKEY == ''||$PRIVATEKEY == ''){
$BUGREPORT = false; 
}
?>
