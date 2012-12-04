<?php
session_start();
include "config.php";
$ranName=str_shuffle($_SESSION['randNum']);
file_put_contents("./downloads/".$ranName.".stamp", time());
if(is_dir("./uploads/".$_SESSION['randNum'])){
shell_exec("cd ./uploads/".$_SESSION['randNum']."; tar cfz ../../downloads/".$ranName.".tgz * --exclude='OCS' --exclude='time.stamp';");
echo json_encode("Download m&ouml;glich:<br><a href='".$HOSTURL."/downloads/".$ranName.".tgz' target='_blank'>Archiv herunterladen</a>");
}else{
echo json_encode("Kein Download m&ouml;glich, bitte erst einmal Dateien mit Inhalt füllen und eine Funktion ausf&uuml;hren!");
}
?>
