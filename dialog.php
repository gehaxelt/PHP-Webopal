<?php
session_start();
include "config.php";
require('markdown.php');
$page="";

if(isset($_GET["page"])){$page=$_GET["page"];}

if($page=="download"){
	echo json_encode(Array("title"=>$page,"text"=>download()));
}else{
	echo json_encode(Array("title"=>$page,"text"=>getMD(strtoupper($page))));
}

function getMD($s){
if(file_exists($s.".md")){
		return MARKDOWN(file_get_contents($s.".md"));
	}else{
		return "Die Datei ".$s.".md existiert leider nicht.";
	}
}

function download(){
	//generate a random name for the download archive
	$ranName=str_shuffle($_SESSION['randNum']);
	file_put_contents("./downloads/".$ranName.".stamp", time());

	//if there is something to archive, archive it and send the download link
	//else tell the user that there is nothing to archive
	if(is_dir("./uploads/".$_SESSION['randNum'])){
		shell_exec("cd ./uploads/".$_SESSION['randNum']."; tar cfz ../../downloads/".$ranName.".tgz * --exclude='OCS' --exclude='time.stamp';");
		return "Download m&ouml;glich:<br><a href='".$HOSTURL."/downloads/".$ranName.".tgz' target='_blank'>Archiv herunterladen</a>";
	}else{
		return"Kein Download m&ouml;glich, bitte erst einmal Dateien mit Inhalt f&uuml;llen und eine Funktion ausf&uuml;hren!";
	}
}
?>
