<?php
session_start();
include "config.php";
require('markdown.php');
$page="";

if(isset($_GET["page"])){$page=$_GET["page"];}

if($page=="download"){
	echo json_encode(Array("title"=>$page,"text"=>download()));
}else if($page=="update"){
if(isset($_GET['implInput'])) {$_SESSION['implInput']=$_GET['implInput'];}
if(isset($_GET['signInput'])) {$_SESSION['signInput']=$_GET['signInput'];}
if(isset($_GET['runFunction'])) {$_SESSION['runFunction']=$_GET['runFunction'];}
if(isset($_GET['fileName'])) {$_SESSION['fileName']=$_GET['fileName'];}
if(isset($_GET['focus'])) {$_SESSION['focus']=$_GET['focus'];}
if(isset($_GET['structnr'])) {
$_SESSION['structnr']=$_GET['structnr'];
if(isset($_GET['file'])) {$_SESSION['fileName'][$_GET['file']]=substr($_SESSION['randNum'],0,4)."datei".$_GET['file'];}
}
if(isset($_GET["delete"])){
$i=$_GET["delete"];
if(isset($_SESSION['implInput'][$i])) {unset($_SESSION['implInput'][$i]);}
if(isset($_SESSION['signInput'][$i])) {unset($_SESSION['signInput'][$i]);}
if(isset($_SESSION['fileName'][$i])) {unset($_SESSION['fileName'][$i]);}
}

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
	global $HOSTURL;
	//generate a random name for the download archive
	$ranName=str_shuffle($_SESSION['randNum']);
	file_put_contents("./tmp/downloads/".$ranName.".stamp", time());

	//if there is something to archive, archive it and send the download link
	//else tell the user that there is nothing to archive
	if(is_dir("./tmp/uploads/".$_SESSION['randNum'])){
		shell_exec("cd ./tmp/uploads/".$_SESSION['randNum']."; tar cfz ../../downloads/".$ranName.".tgz * --exclude='OCS' --exclude='time.stamp';");
		return "Download m&ouml;glich:<br><a href='".$HOSTURL."/tmp/downloads/".$ranName.".tgz' target='_blank'>Archiv herunterladen</a>";
	}else{
		return"Kein Download m&ouml;glich, bitte erst einmal Dateien mit Inhalt f&uuml;llen und eine Funktion ausf&uuml;hren!";
	}
}
?>
