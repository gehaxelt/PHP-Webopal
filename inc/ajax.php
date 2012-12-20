<?php
session_start();
include "../config.php";
require('markdown.php');
$page="";
//Sessionexpiration
$_SESSION['sessionstart'] = time();
//Escaping all variables
//if(isset($_GET['implInput'])) { $_GET['implInput']=htmlentities($_GET['implInput']); }
//if(isset($_GET['signInput'])) { $_GET['signInput']=htmlentities($_GET['signInput']); }
//if(isset($_GET['fileName'])) { $_GET['fileName']=htmlentities($_GET['fileName']); }
if(isset($_GET['runFunction'])) { $_GET['runFunction']=htmlentities($_GET['runFunction']); }
if(isset($_GET['structnr'])) { $_GET['structnr']=htmlentities($_GET['structnr']); }
if(isset($_GET['file'])) { $_GET['file']=htmlentities($_GET['file']); }
if(isset($_GET['delete'])) { $_GET['delete']=htmlentities($_GET['delete']); }
if(isset($_GET['page'])) { $_GET['page']=htmlentities($_GET['page']); }
if(isset($_GET['oasys'])) { $_GET['oasys']=htmlentities($_GET['oasys']); }
if(isset($_GET['actTab'])) { $_GET['actTab']=htmlentities($_GET['actTab']); }
if(isset($_GET['debug'])) { $_GET['debug']=htmlentities($_GET['debug']); }else{$_GET['debug']=false;}

if(isset($_GET["page"])){$page=$_GET["page"];}

if($page=="trashmail"){
	if(count($FORBIDDENMAIL)>0){
		$email = $_GET['email'];
		if(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email)){
			if(preg_match("~(".implode(')|(',$FORBIDDENMAIL).")~",$email,$matches)){
				echo json_encode("Domain ".$matches[0]." nicht erlaubt");
			}else{
				echo json_encode(true);
			}
		} else {
			echo json_encode("Das ist keine Email");
		}
	}else{
		echo json_encode(true);
	}
} else if($page=="download"){
	echo json_encode(Array("title"=>$page,"text"=>download()));
}else if($page=="update"){
	if(isset($_GET['fileName'])) {$_SESSION['fileName']=  array_slice($_GET['fileName'] ,0,$MAXFILES,true);}
	if(isset($_GET['implInput'])) {$_SESSION['implInput']=fixIMPLandSIGN(array_slice($_GET['implInput'],0,$MAXFILES,true),$_SESSION['fileName']);}
	if(isset($_GET['signInput'])) {$_SESSION['signInput']=fixIMPLandSIGN(array_slice($_GET['signInput'],0,$MAXFILES,true),$_SESSION['fileName']);}
	if(isset($_GET['runFunction'])) {$_SESSION['runFunction']=$_GET['runFunction'];}
	if(isset($_GET['actTab'])) {$_SESSION['actTab']=$_GET['actTab'];}
	if(isset($_GET['debug'])) {$_SESSION['debug']=$_GET['debug'];}
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
	if(isset($_GET["oasys"])){
		if(isset($_SESSION['implInput'])) {
			echo json_encode(runOasys($_SESSION['implInput'],$_SESSION['signInput'],$_SESSION['runFunction'],$_SESSION['fileName'],$_SESSION['debug']));
		} else {
			echo json_encode("Deine Session ist abgelaufen. Bitte einmal mit F5 neuladen.");
		}
	}
}else if($page=="checkCaptcha"){
	echo 	json_encode(checkCaptcha());
}else if($page=="issueList"){
	echo 	json_encode(getIssues());
}else if($page=="issueForm"){
	echo 	json_encode(getIssueForm());
}else if($page=="login"){
	echo 	json_encode(getLoginForm());
}else if($page=="loginCheck"){
	echo 	json_encode(loginCheck($_GET['user'],$_GET['pw']));
}else if($page=="getFolders"){
	if($_SESSION['loggedIn']){
	echo 	json_encode(getFolders($_SESSION['loggedInPath']));
	}else{
	echo "Not logged in. Please reload the page.";
	}
}else{
	$text=getMD(strtoupper($page));
	if(isset($_GET['since'])){
		if($_GET['since']!=""){
			if(preg_match('~'.$_GET['since'].'~',$text)){
				$split=preg_split('~.*h2.*'.$_GET['since'].'~',$text,2);
				$text=$split[0];
			}
		}
	}
	echo json_encode(Array("title"=>$page,"text"=>$text));
}

function getMD($s){
if(file_exists("../markdown/".$s.".md")){
		return MARKDOWN(file_get_contents("../markdown/".$s.".md"));
	}else{
		return "Die Datei ".$s.".md existiert leider nicht.";
	}
}

function download(){
	global $HOSTURL, $RUNMAX,$TMPDIR;
	//generate a random name for the download archive
	$ranName=str_shuffle($_SESSION['randNum']);
	file_put_contents("../".$TMPDIR."/downloads/".$ranName.".stamp", time());

	//if there is something to archive, archive it and send the download link
	//else tell the user that there is nothing to archive
	if(is_dir("../".$TMPDIR."/files/".$_SESSION['randNum'])){
		shell_exec("cd ../".$TMPDIR."/files/".$_SESSION['randNum']."; tar cfz ../../../".$TMPDIR."/downloads/".$ranName.".tgz * --exclude='OCS' --exclude='time.stamp';");
		return "Download m&ouml;glich:<br><a href='".$HOSTURL."/".$TMPDIR."/downloads/".$ranName.".tgz' target='_blank'>Archiv herunterladen</a>";
	}else{
		return "Kein Download m&ouml;glich, bitte erst einmal Dateien mit Inhalt f&uuml;llen und eine Funktion ausf&uuml;hren!";
	}
}

function fixIMPLandSIGN($arr,$names) {
	foreach($arr as $i => $a){
		$arr[$i]=preg_replace('/((IMPLEMENTATION|SIGNATURE)\s*)([A-Za-z0-9]*)(.*\n)/',"$1".$names[$i]."$4",$a);
	}
	return $arr;
}
function runOasys($impls,$signs,$cmd,$names,$debugOpal) {
	global $TIMEOUT,$TIMEOUTTXT,$ADVERTCOMMENT,$TMPDIR,$RUNMAX;

	if($cmd==""){return Array("log"=>"Keine Funktion(en) angegeben.");}
	
	/* Generate a random number for the directory and create the directory */
	for($i=0;$i<5;$i++){
		$ranFile = md5($i.time().str_shuffle(time()));
		$dirStr = "../".$TMPDIR."/files/".$ranFile;
		if(!is_dir($dirStr)){break;}else if($i==4){return Array("log"=>"Wir konnten leider keinen Ordner für dich anlegen. Probier es nochmal!");}
	}
	//$_SESSION['randNum']=$ranFile; Is this really necessary?
	mkdir($dirStr);

	file_put_contents($dirStr."/time.stamp", time());

	/* Create impl and sign files for every structure with a non empty impl */
	foreach($impls as $i => $impl){
		if($impls[$i]!=""){
			$implStr="";$signStr="";
			
			/* Check if structure contains bad things */
			$pattern = '~(.+Com.+)|(INLINE)|(DEBUG)|(.+Stream.+)|(BasicIO)|(LineFormat)|(Commands)|(.+File.+)|(.+Process.+)|(.+Signal.+)|(.+User.+)|(.+Wait.+)|(.+Unix.+)~sm'; 
			if(preg_match($pattern, $impls[$i].$signs[$i].$cmd)){return Array("log"=>"Es wurden unerlaubte Strukturen entdeckt. Du Lausbub! Versuchst du unseren Server zu hacken?");}

			/* Check if name contains bad things */
			$pattern = '~[^a-zA-Z0-9]~sm'; 
			if(preg_match($pattern, $names[$i])){return Array("log"=>"Bitte in den Strukturnamen nur Zeichen aus den Gruppen [A-Z], [a-z] oder [0-9] verwenden");}

			if($names[$i]==""){$names[$i]="keinName".substr($_SESSION['randNum'],0,4).rand(1,100);}

			/* Create impl and sign files for the structure */
			if(preg_match('/SIGNATURE/',$signs[$i])===0){$signStr = "SIGNATURE ".$names[$i];}
			if(preg_match('/IMPLEMENTATION/',$impls[$i])===0){$implStr = "IMPLEMENTATION ".$names[$i];}
			
			file_put_contents($dirStr."/".$names[$i].".sign",$ADVERTCOMMENT."\n".$signStr."\n".str_replace("\r\n","\n",$signs[$i]));
			file_put_contents($dirStr."/".$names[$i].".impl",$ADVERTCOMMENT."\n".$implStr."\n".str_replace("\r\n","\n",$impls[$i]));
		}
	}
	
	//Split commands at ;
	$cmd=str_replace("&quot;","\"",$cmd);
	$cmd=str_replace("&lt;","<",$cmd);
	$cmd=str_replace("&gt;",">",$cmd);
	$cmds=explode(";",$cmd);
	if(count($cmds)>$RUNMAX) {
		return Array("log"=>"Du kannst maximal ".$RUNMAX." Funktionen hintereinander ausführen."); //senseless error description
	}
	$runOrder="";
	$lastFocus="";
	$added=Array();
	$focus="";
	$extension=".sign";
	if($debugOpal){$extension=".impl";}
	foreach($cmds as $c){
		$focussed=false;
		$k=explode("=>",$c);
		if($c!=""){
			if(count($k)==1){
				$searchToken=preg_replace('/\s*\(.+\)\s*/','',$k[0]);
				$cmdInImpl = preg_grep('/.*DEF\s+'.$searchToken.'\s*[\(=\.].*/u', $impls);
				if(count($cmdInImpl)>1){return Array("log"=>"Die Funktion '$c' wurde mehrmals definiert.<br>Bitte mit Hilfe von '[structureName]=>$c' in der Aufrufzeile einen Focus erzielen.");}
				else if(count($cmdInImpl)==1){
					$focus=array_keys($cmdInImpl);
					$focus=$focus[0];
					$focussed=true;
				}else{
					$focus='';
				}
			}else if(count($k)==2){
				$k[0]=preg_replace("/(.impl)|(.sign)|(\]|\[)/","",$k[0]);
				$c=$k[1];
				$focus=array_search($k[0],$names);
				$focussed=true;
			}else{
				return Array("log"=>"Deine Aufrufzeile ist nicht wohl formatiert. Bitte die Funktionen durch Semikolons separieren!");
			}
			
			if($focus!=""||$focussed){
				if(!in_array($names[$focus],$added)){
				$runOrder.="a ".$names[$focus]."\n";
				$added[]=$names[$focus];
				}
				if($lastFocus!=$names[$focus]){

				$runOrder.="f ".$names[$focus].$extension."\n";
				$lastFocus=$names[$focus];
				}
			}
			$runOrder.="e ".$c."\n";
		}
	}

	
	/* Run focussed Structure */
	file_put_contents($dirStr."/runOpal.exec",$runOrder);
	shell_exec("cd ".	$dirStr."; timeout ".$TIMEOUT." oasys < runOpal.exec > runOpal.log;echo '".$TIMEOUTTXT."' >> runOpal.log");
	
	/* Return log */
	$result=file_get_contents($dirStr."/runOpal.log");
	$result=preg_replace("~(\n.*quit.*\n.*)~","",$result);
	file_put_contents($dirStr."/runOpal.log", $ADVERTCOMMENT."\n\n".$result);
	$result=preg_replace("/\n/","\n\t\t",$result);
	$result=preg_replace("~(>a.+\n..)||(starting.+\n..)|(loading.+\n..)|(checking.+\n..)|(compiling.+\n..)~","",$result);
	$result=preg_replace("/\n.*(>[ef])/","\n$1",$result);
	$result=preg_replace("/\t/","&nbsp;",$result);
	$results=explode("\n",$result);
	$c=0;
	$retError=Array();
	foreach($results as $key=>$result){
		if(preg_match("/(ERROR|WARNING) \[((.+.)\.(.+.) )?at (\d+)\.(\d+)(-(\d+)\.(\d+))?\]/",$result,$error)){
			if($error[3]!=""){
			if($error[4]=="sign"){$error[5]=$error[5]-1;}
			$err=Array("file"=>$error[3],"type"=>$error[4],"fromLine"=>max(0,$error[5]-3),"fromChar"=>$error[6]-1,"toLine"=>max(0,$error[5]-3),"toChar"=>$error[6]);
			if(isset($error[7])){
				$err["toLine"]=max(0,$error[8]-3);
				$err["toChar"]=$error[9];
			}
			
			$retError[]=$err;
			$results[$key]=preg_replace("/((ERROR|WARNING) \[.+.\])/","<a href='#' class='errorJump' value='".json_encode($err)."'>$1</a>",$result);	
			}
		}
	}
	return Array("log"=>implode("<br>",$results),"err"=>json_encode(justonetime($retError,Array("file","type","fromLine")),JSON_FORCE_OBJECT));
}

function justonetime($a,$b){
$compare=array();$r=array();
foreach($a as $array){
$c="";
foreach($b as $key){
$c.=$array[$key];
}
if(!in_array($c,$compare)){
$r[]=$array;
$compare[]=$c;
}
}

return $r;
}

function loginCheck($user,$pw){
	global $TMPDIR,$_SESSION;
	$users=unserialize(file_get_contents("../".$TMPDIR.'/users'));
	if(isset($users[$user])){
		if($pw==sha1($users[$user]["pw"].$_SESSION['randNum'])){
			$_SESSION['loggedIn']=true;
			$_SESSION['loggedInAs']=$user;
			$_SESSION['loggedInPath']=$users[$user]["path"];
			$users[$user]["lastTime"]=time();
			file_put_contents("../".$TMPDIR.'/users',serialize($users));
			return Array("success"=>true,"msg"=>"");
		}else{
			return Array("success"=>false,"msg"=>"User/Passwort stimmen nicht überein");
	}
	}else{
		return Array("success"=>false,"msg"=>"User/Passwort stimmen nicht überein");
	}
}

// function for fetching issues from github, used for bug reporting feature
function getIssues(){
global $ISSUEUSER,$ISSUEREPO;
include "../tools/githubapi/vendor/autoload.php";
$echo=Array();
$client = new Github\Client();
$iss1 = $client->api('issue')->all($ISSUEUSER,$ISSUEREPO,array('state'=>'open'));
$iss2 = $client->api('issue')->all($ISSUEUSER,$ISSUEREPO,array('state'=>'closed',"labels"=>"postponed,"));
$issues = array_merge($iss1,$iss2);
foreach($issues as $issue){
$token="";$token2="";
if($issue["pull_request"]["html_url"]!=null){
	$token="&nbsp;&nbsp;<small><small>(Pull Request)</small></small>";
}else if($issue["state"]=="closed"){
	$token="&nbsp;&nbsp;<small><small>(Postponed)</small></small>";
	$token2="<b>ACHTUNG:</b> Dieser Issue wird nicht weiter verfolgt, um zu erfahren warum:<br>";
}
$echo[$issue["number"]]="<h3>"."#".Intval($issue["number"]).": ".htmlentities($issue["title"], ENT_QUOTES, 'UTF-8').$token."</h3>
		<div class='issue'>
			<p>Beschreibung Problem:</p>
			<div class='issueDescription'>".MARKDOWN(htmlentities($issue["body"], ENT_QUOTES, 'UTF-8'))."</div>
			<p class='issueInfo'>$token2 Lies die komplette Diskussion zu dem Issue <a href='".htmlentities($issue["html_url"])."' target='_blank'>hier auf Github</a></p>
		</div>";
}

krsort($echo,SORT_NUMERIC);

return implode("",$echo);
}

function getIssueForm(){
$error="";
$echo='<form id="reportData">
			<div><label for="title">Titel: </label><input type="text" size="40" name="title"></div>
			<div><label for="email">Email (opt.):</label><input type="text" size="40" name="email"></div>
			<div><label for="type">Art: </label><input type="radio" name="type" value="bug"> Bug <input type="radio" name="type" value="idea"> Idee</div>
			<div><label for="description">Beschreibung:</label><br>
			<div><textarea style="width:100%;" rows="10" name="description"></textarea></div><br>
			<div><input type="checkbox" name="agree"><label for="agree">Ich versichere, dass ich mir die Issueliste links angeguckt habe und keine Dopplung auftritt.</label></div><br>
			<div id="reCaptcha"></div>
			<div style="color: red;" id="captchaStatus">&nbsp;</div>
			<input id="issueSubmit" name="issueSubmit" type="button" value="Absenden">
		 </form>';
return $echo;
}

function getLoginForm(){
$error="";
$echo='<form id="loginData">
			<div><label for="user">User: </label><input type="text" size="40" name="user" id="user"></div>
			<div><label for="pw">Password: </label><input type="password" size="40" name="pw" id="pw"></div>
			<input id="loginSubmit" name="loginSubmit" type="button" value="Absenden">
		 </form>';
return $echo;
}

function checkCaptcha(){
	global $_POST,$_GET,$ISSUEUSER,$ISSUEREPO,$GITHUBUSER,$GITHUBPW,$PUBLICKEY,$PRIVATEKEY;
	require_once('../tools/recaptchalib.php');

	$resp = recaptcha_check_answer ($PRIVATEKEY,
		                             $_SERVER["REMOTE_ADDR"],
		                             $_POST["recaptcha_challenge_field"],
		                             $_POST["recaptcha_response_field"]);

	if ($resp->is_valid) {
		require_once "../tools/githubapi/vendor/autoload.php";
		$echo="";
		$client = new Github\Client();
		$client->authenticate($GITHUBUSER,$GITHUBPW,Github\Client::AUTH_HTTP_PASSWORD);
		$token="";
		if($_POST["type"]=="idea"){$token="[FEATURE] ";}else{$token="[BUG] ";}
		$github=$client->api('issue')->create($ISSUEUSER, $ISSUEREPO, array('title' => $token.$_POST["title"], 'body' => "Useridee:\n".htmlentities($_POST["description"])));
		$_POST['success']=true;
		$_POST['succ']="<h3>".htmlentities("#".$github["number"].": ".$github["title"])."</h3>
		<div class='issue'>
			<p>Beschreibung Problem:</p>
			<p class='issueDescription'>".MARKDOWN(htmlentities($github["body"]))."</p>
		</div>";
		return $_POST;
	} else {
		return "Captcha falsch:".$resp->error;
	}
}

function getFolders($path){
global $TMPDIR;
$echo='';
foreach (new DirectoryIterator('../'.$TMPDIR.'/userfiles/'.$path) as $fn) {
    if (!$fn->isDot()) {
     $echo.='<a href="#" class=".changeDir" name="';
     $echo.=$fn->getFilename();
     $echo.='">';
     $echo.=$fn->getFilename();
     $echo.='</a><br>';     
    }
 }
 
return $echo;
}

?>
