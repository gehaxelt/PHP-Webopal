<?php
session_start();
/**
Simple logout script.
Destroy and unset the current session and redirect the user to the main page.
**/
session_unset();
session_destroy();
header("Location: ../index.php");
?>
