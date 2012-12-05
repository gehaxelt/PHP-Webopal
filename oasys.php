<?php
session_start();
include "config.php";

/* Update Session Variables */
if(isset($_GET['implInput'])) {$_SESSION['implInput']=$_GET['implInput'];}
if(isset($_GET['signInput'])) {$_SESSION['signInput']=$_GET['signInput'];}
if(isset($_GET['runFunction'])) {$_SESSION['runFunction']=$_GET['runFunction'];}
if(isset($_GET['fileName'])) {$_SESSION['fileName']=$_GET['fileName'];}
if(isset($_GET['focus'])) {$_SESSION['focus']=$_GET['focus'];}

/* Return interpretation of OPAL code */
echo json_encode(runOasys($_SESSION['implInput'],$_SESSION['signInput'],$_SESSION['runFunction'],$_SESSION['fileName'],$_SESSION['focus']));
//echo json_encode($_GET['runFunction'].$_GET['focus']);

function runOasys($impls,$signs,$cmd,$names,$focus) {
	global $TIMEOUT,$TIMEOUTTXT;

	if($cmd==""){return "Keine Funktion angegeben.";}
	if($impls[$focus]==""){return "Fokussierte Implementation ist leer.";}
	
	/* Generate a random number for the directory and create the directory */
	for($i=0;$i<5;$i++){
		$ranFile = md5($i.time().str_shuffle(time()));
		$dirStr = "./uploads/".$ranFile;
		if(!is_dir($dirStr)){break;}else if($i==4){return "Wir konnten leider keinen Ordner für dich anlegen. Probier es nochmal!";}
	}
	$old=$_SESSION['randNum'];
	$_SESSION['randNum']=$ranFile;
	mkdir($dirStr);

	file_put_contents($dirStr."/time.stamp", time());

	/* Create impl and sign files for every structure with a non empty impl */
	for($i=0;$i<$_SESSION['structnr'];$i++){
		if($impls[$i]!=""){

			/* Check if structure contains bad things */
			$pattern = '~(.+Com.+)|(DEBUG)|(.+Stream.+)|(BasicIO)|(LineFormat)|(Commands)|(.+File.+)|(.+Process.+)|(.+Signal.+)|(.+User.+)|(.+Wait.+)|(.+Unix.+)~sm'; 
			if(preg_match($pattern, $impls[$i].$signs[$i].$cmd)){return "Es wurden unerlaubte Strukturen entdeckt.";}

			/* Check if name contains bad things */
			$pattern = '~[^a-zA-Z0-9]~sm'; 
			if(preg_match($pattern, $names[$i])){return "Bitte in den Dateinamen nur Zeichen aus folgenden Gruppen [A-Z], [a-z] oder [0-9] verwenden";}

			$impls[$i]=preg_replace('/IMPLEMENTATION(.+.)\n/',"",$impls[$i]);
			$signs[$i]=preg_replace('/SIGNATURE(.+.)\n/',"",$signs[$i]);

			/* Create impl and sign files for the structure */
			$signStr = "SIGNATURE ".$names[$i];
			$implStr = "IMPLEMENTATION ".$names[$i];
			file_put_contents($dirStr."/".$names[$i].".sign",$signStr."\n".str_replace("\r","\n",$signs[$i]));
			file_put_contents($dirStr."/".$names[$i].".impl",$implStr."\n".str_replace("\r","\n",$impls[$i]));		
		}
	}
	
	//Split commands at ;
	$cmd=str_replace(";","\ne ",$cmd);
	
	/* Run focussed Structure */
	file_put_contents($dirStr."/".$names[$focus].".exec","a ".$names[$focus]."\nf ".$names[$focus].".impl\ne ".$cmd);
	shell_exec("cd ".$dirStr."; timeout ".$TIMEOUT." oasys < ".$names[$focus].".exec > ".$names[$focus].".log;echo '".$TIMEOUTTXT."' >> ".$names[$focus].".log");
	
	/* Return log */
	$result=file_get_contents($dirStr."/".$names[$focus].".log");
	$result=preg_replace("/\n/","\n  ",$result);
	$result=preg_replace("~(>a.+\n..)||(starting.+\n..)|(loading.+\n..)|(checking.+\n..)|(compiling.+\n..)|(.+.quit.\n..You.+.)~","",$result);
	$result=preg_replace("/\n.*(>[ef])/","\n$1",$result);
	return $result;
}
?>
