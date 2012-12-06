<?php
$HOSTURL=''; //Set the host url here, e.g. http://opal.test.me/
$IMPRESSUM=''; //Set the url to your impressum, if necessary.
$MINFILES=2; //Minimum / standard number of structures
$MAXFILES=3; //Number of the Maximum Structures allowed
$TIMEOUT=10; //Number of seconds which the program is allowed to run
$TIMEOUTTXT='Your program maybe timed out (>'.$TIMEOUT.'s)'; //text shown, if program did timeout
$SESSIONTIMEOUT=600; //time in seconds until the session expire
$EXAMPLECODE_IMPL = "DEF hello == \"Hello World!\"";
$EXAMPLECODE_SIGN = "FUN hello : denotation";
$EXAMPLECODE_CMD = "hello";
$VERSION = "v0.2";
$ADVERTCOMMENT = "-- created with WebOpal ".$VERSION." (https://github.com/gehaxelt/PHP-Webopal) on Server ".$HOSTURL;
?>
