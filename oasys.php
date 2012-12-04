<?php
session_start();
if(isset($_GET['impl_eingabe'])) {$_SESSION['impl_eingabe']=$_GET['impl_eingabe'];}
if(isset($_GET['sign_eingabe'])) {$_SESSION['sign_eingabe']=$_GET['sign_eingabe'];}
if(isset($_GET['execute'])) {$_SESSION['cmd']=$_GET['execute'];}
if(isset($_GET['name'])) {$_SESSION['name']=$_GET['name'];}
if(isset($_GET['focus'])) {$_SESSION['focus']=$_GET['focus'];}
include "config.php";
echo json_encode(runOasys($_GET['impl_eingabe'],$_GET['sign_eingabe'],$_GET['execute'],$_GET['name'],$_GET['focus']));
	function runOasys($imps,$signs,$cmd,$names,$focus) {
		global $TIMEOUT,$TIMEOUTTXT;
		if($cmd==""){return "Keine Funktion angegeben.";}
		if($imps[$focus]==""){return "Fokussierte Implementation ist leer.";}

		/* Generate a random number for the directory and create the directory */
		$ranFile = md5(time().str_shuffle(time()));
		$_SESSION['randNum']=$ranFile;
		$dirStr = "./uploads/".$ranFile;
		mkdir($dirStr);

		file_put_contents($dirStr."/time.stamp", time());

		/* Create impl and sign files for every structure with a non empty impl */
		for($i=0;$i<$_SESSION['structnr'];$i++){
			if($imps[$i]!=""){

				/* Check if structure contains bad things */
				$pattern = '~(.+Com.+)|(DEBUG)|(.+Stream.+)|(BasicIO)|(LineFormat)|(Commands)|(.+File.+)|(.+Process.+)|(.+Signal.+)|(.+User.+)|(.+Wait.+)|(.+Unix.+)~sm'; 
				if(preg_match($pattern, $imps[$i].$signs[$i].$cmd)){return "Es wurden unerlaubte Strukturen entdeckt.";}

				/* Check if name contains bad things */
				$pattern = '~[^a-zA-Z0-9]~sm'; 
				if(preg_match($pattern, $names[$i])){return "Bitte in den Dateinamen nur Zeichen aus folgenden Gruppen [A-Z], [a-z] oder [0-9] verwenden";}

				/* Create impl and sign files for the structure */
				$signStr = "SIGNATURE ".$names[$i];
				$implStr = "IMPLEMENTATION ".$names[$i];
				file_put_contents($dirStr."/".$names[$i].".sign",$signStr."\n".str_replace("\r","\n",$signs[$i]));
				file_put_contents($dirStr."/".$names[$i].".impl",$implStr."\n".str_replace("\r","\n",$imps[$i]));		
			}
		}
		/* Run focussed Structure */
		file_put_contents($dirStr."/".$names[$focus].".exec","a ".$names[$focus]."\nf ".$names[$focus].".impl\ne ".$cmd);
		shell_exec("cd ".$dirStr."; timeout ".$TIMEOUT." oasys < ".$names[$focus].".exec > ".$names[$focus].".log;echo '".$TIMEOUTTXT."' >> ".$names[$focus].".log");
		/* Return log */
		$result=file_GET_contents($dirStr."/".$names[$focus].".log");
		$result=explode(">e ",$result);
		$result=preg_replace("~(checking.+\n)|(compiling.+\n)~","",$result);
		$result=str_replace($names[$focus].".impl>^D (quit)\n".$TIMEOUTTXT,"",$result[1]);
		return $result;
}
?>
