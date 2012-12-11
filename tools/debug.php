<?php
session_start();
include "../config.php";

//If debug mode is enabled, print content of $_SESSION
if($DEBUGMODE) {
	echo "<pre>";
	var_dump($_SESSION);
	echo "</pre>";
}
?>
