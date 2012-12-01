<?php
session_start();
include 'config.php';
$_SESSION['sessionstart'] = time();

/* Check if $_SESSION is set, if not initialize them */
if(!isset($_SESSION['cmd'])) {$_SESSION['cmd']=""; }
if(!isset($_SESSION['focus'])) {$_SESSION['focus']=0; }
if(!isset($_SESSION['randNum'])) {$_SESSION['randNum']=md5(time().str_shuffle(time()));}

/* Check if $_POST is set, if yes, update $_SESSION */
if(isset($_POST['impl_eingabe'])) {$_SESSION['impl_eingabe']=$_POST['impl_eingabe'];}
if(isset($_POST['sign_eingabe'])) {$_SESSION['sign_eingabe']=$_POST['sign_eingabe'];}
if(isset($_POST['execute'])) {$_SESSION['cmd']=$_POST['execute'];}
if(isset($_POST['name'])) {$_SESSION['name']=$_POST['name'];}
if(isset($_POST['focus'])) {$_SESSION['focus']=$_POST['focus'];}

$jsinit="";
for($i=0;$i<$MAXFILES;$i++){
	/* initialize further $_SESSION if necessary */
	if(!isset($_SESSION['name'][$i])) {
		$_SESSION['name'][$i]=""; 
	}else{
		if($_SESSION["name"][$i]==""){
			$_SESSION["name"][$i]=substr($_SESSION['randNum'],0,4)."datei".$i;
		}
	}
	if(!isset($_SESSION['impl_eingabe'][$i])) {$_SESSION['impl_eingabe'][$i]=""; }
	if(!isset($_SESSION['sign_eingabe'][$i])) {$_SESSION['sign_eingabe'][$i]=""; }

	/* If the structure has no name, create one */

	
	/* initialize editAreas */
	$jsinit .= '
	editAreaLoader.init({
		id : "impl_eingabe'.$i.'"		// textarea id
		,syntax: "opal"			// syntax to be uses for highgliting
		,start_highlight: true		// to display with highlight mode on start-up
		,min_width: 450
		,min_height: 200
		,allow_toggle: false
	});
        editAreaLoader.init({
                id : "sign_eingabe'.$i.'"             // textarea id
                ,syntax: "opal"                    // syntax to be uses for highgliting
                ,start_highlight: true          // to display with highlight mode on start-up
		,min_width: 450
		,min_height: 200
        	,allow_toggle: false
	});';
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<title>WebOpal v0.1a</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
	<script language="javascript" type="text/javascript" src="editarea/edit_area/edit_area_full.js"></script>
	<script language="javascript" type="text/javascript">
	<?php echo $jsinit; ?>
    	$(function() {
		$("#accordion").accordion({collapsible:false,heightStyle: "content", event: "mouseup",active : <?php echo $_SESSION['focus'];?>});
	});
	</script>
	<script language="javascript" type="text/javascript">
	  (function() {
	    var cx = '014104389563113645663:vm6azr2-wkg';
	    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
	    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
		'//www.google.de/cse/cse.js?cx=' + cx;
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
	  })();
	</script>
</head>
<body>
	<div id="wrapper">
		<h1>WebOpal by Sebastian Neef und Tim M&uuml;ller</h1>
		<span>Bitte in der Impl bzw. Sign die IMPLEMENTATION bzw. SIGNATURE weglassen. <a href="<?php echo htmlentities($IMPRESSUM); ?>">Impressum</a> </span>
		<form action="index.php" method="post">
				<div id="accordion">
				<?php
				/* Print Signature & Implementation Areas */
				for($i=0;$i<$MAXFILES;$i++){
					if($i==$_SESSION['focus']){$checked="checked";}else{$checked="";}
					echo '
					<h3 class="filename">
						Struktur '.($i+1).'; Name: <input id="name'.$i.'" class="name_eingabe" name="name['.$i.']" value="'.htmlentities($_SESSION['name'][$i]).'">
						<input type="radio" name="focus" value="'.$i.'" '.$checked.'> Fokus
					</h3>
					<div class="struccontainer" style="padding:10px;">
					<div class="implcontainer">
	    					Eingabe f&uuml;r den Implementationsteil:
						<textarea class="quadrat impl_eingabe" id="impl_eingabe'.$i.'" name="impl_eingabe['.$i.']" cols="50" rows="10">'.htmlentities($_SESSION['impl_eingabe'][$i]).'</textarea>
	    				</div>
					<div class="signcontainer">
						Eingabe f&uuml;r den Signaturteil:
						<textarea class="quadrat sign_eingabe" id="sign_eingabe'.$i.'" name="sign_eingabe['.$i.']" cols="50" rows="10">'.htmlentities($_SESSION['sign_eingabe'][$i]).'</textarea>
	    				</div>
					</div>';
				}
				?>
				</div>
				<br>
				<div id="funccontainer">
					Eingabefeld: "Funktionsaufruf func(var1, var2, ...)":<br>
					<input name="execute" type="text" size="43" maxlength="30" value="<?php echo htmlentities($_SESSION['cmd']);?>">
				</div>
				<div id="sendcontainer">
					Zum Ausf&uuml;hren den Knopf dr&uuml;cken:<br>
					<input type="submit" name="button1" value="Ausf&uuml;hren" >
				</div>
				<div id="outputcontainer">
					<textarea name="output" cols="110" rows="10"><?php
							echo htmlentities(runOasys($_SESSION['impl_eingabe'],$_SESSION['sign_eingabe'],$_SESSION['cmd'],$_SESSION['name'],$_SESSION['focus'])); 
										?>
					</textarea>
				</div>
		</form>
		<div id="download">
			<span>Download als Tarball:</span>
			<form action="index.php" id="downloadform" method="post">
				<input type="submit" name="Download" value="Download">
			</form>
			<?php
				if(isset($_POST['Download'])) {
					echo downloadURL();
				}
			?>
		</div>
		<br>Bibliotheca Opalica Suche:
       		<div id="customsearch">
			<div class="gcse-search"></div>
        	</div>
		<br />		
		<div id="github">Fork us on GitHub:<iframe src="http://ghbtns.com/github-btn.html?user=gehaxelt&repo=PHP-Webopal&type=fork&count=true"
  allowtransparency="true" frameborder="0" scrolling="0" width="95" height="20"></iframe></div> <br />
	</div>
	<?php include "piwik.php"; ?>
</body>

</html>

<?php
	/* Generate Tarball of Code */
	function downloadURL() {
		global $HOSTURL;
		$ranName=str_shuffle($_SESSION['randNum']);
		file_put_contents("./downloads/".$ranName.".stamp", time());
		shell_exec("cd ./uploads/".$_SESSION['randNum']."; tar cfz ../../downloads/".$ranName.".tgz * --exclude='OCS' --exclude='time.stamp';");
		return "<span><a href='".htmlentities($HOSTURL)."/downloads/".$ranName.".tgz'>Archiv ".$ranName.".tgz downloaden</a></span>";
	}

	/* Run Oasys Code */
	function runOasys($imps,$signs,$cmd,$names,$focus) {
		global $MAXFILES,$TIMEOUT,$TIMEOUTTXT;
		if($cmd==""){return "Keine Funktion angegeben.";}
		if($imps[$focus]==""){return "Fokussierte Implementation ist leer.";}

		/* Generate a random number for the directory and create the directory */
		$ranFile = md5(time().str_shuffle(time()));
		$_SESSION['randNum']=$ranFile;
		$dirStr = "./uploads/".$ranFile;
		mkdir($dirStr);

		file_put_contents($dirStr."/time.stamp", time());

		/* Create impl and sign files for every structure with a non empty impl */
		for($i=0;$i<$MAXFILES;$i++){
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
		$result=file_get_contents($dirStr."/".$names[$focus].".log");
		$result=explode(">e ",$result);
		$result=preg_replace("~(checking.+\n)|(compiling.+\n)~","",$result);
		$result=str_replace($names[$focus].".impl>^D (quit)\n".$TIMEOUTTXT,"",$result[1]);
		return $result;
	}
?>
