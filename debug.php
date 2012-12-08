<?php
session_start();
include "config.php";

if($DEBUGMODE) {
	echo "<pre>";
	var_dump($_SESSION);
	echo "</pre>";
}
?>
