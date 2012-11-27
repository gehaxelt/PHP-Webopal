<?php session_start(); ?>
<?php

include 'config.php';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>WebOpal v0.1</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script language="javascript" type="text/javascript" src="/editarea/edit_area/edit_area_full.js"></script>
	<script language="javascript" type="text/javascript">
	editAreaLoader.init({
		id : "impl_eingabe"		// textarea id
		,syntax: "opal"			// syntax to be uses for highgliting
		,start_highlight: true		// to display with highlight mode on start-up
		,allow_toggle: false
	});
        editAreaLoader.init({
                id : "sign_eingabe"             // textarea id
                ,syntax: "opal"                    // syntax to be uses for highgliting
                ,start_highlight: true          // to display with highlight mode on start-up
        	,allow_toggle: false
	});

</script>
<script>
  (function() {
    var cx = '014104389563113645663:vm6azr2-wkg';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.de/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>
</head>
<?php
	if(!isset($_SESSION['impl_eingabe'])) {$_SESSION['impl_eingabe']=''; }
	if(!isset($_SESSION['sign_eingabe'])) {$_SESSION['sign_eingabe']=''; }
	if(!isset($_SESSION['cmd'])) {$_SESSION['cmd']=''; }
	if(isset($_POST['impl_eingabe'])) {$_SESSION['impl_eingabe']=$_POST['impl_eingabe'];}
	if(isset($_POST['sign_eingabe'])) {$_SESSION['sign_eingabe']=$_POST['sign_eingabe'];}
	if(isset($_POST['execute'])) {$_SESSION['cmd']=$_POST['execute'];}
	if(!isset($_SESSION['randNum'])) {$_SESSION['randNum']=0;}
?>
<body>
	<div id="wrapper">
		<h1>WebOpal by Sebastian Neef und Tim M&uuml;ller</h1>

		<span>Bitte in der Impl bzw. Sign die IMPLEMENTATION bzw. SIGNATURE weglassen. <a href="<?php echo $IMPRESSUM; ?>">Impressum</a> </span>
		<form action="index.php" method="post">
				<div id="implcontainer">
    				Eingabe f&uuml;r den Implementationsteil:
					<textarea class="quadrat" id="impl_eingabe" name="impl_eingabe" cols="50" rows="10"><?php echo htmlentities($_SESSION['impl_eingabe']);?></textarea>
    				</div>
				<div id="signcontainer">
				Eingabe f&uuml;r den Signaturteil
					<textarea class="quadrat" id="sign_eingabe" name="sign_eingabe" cols="50" rows="10"><?php echo htmlentities($_SESSION['sign_eingabe']);?></textarea>
    				</div>
				<div id="funccontainer">
				Eingabefeld: "Funktionsaufruf func(var1, var2, ...)":<br>
					<input name="execute" type="text" size="43" maxlength="30" value="<?php echo htmlentities($_SESSION['cmd']);?> ">
				</div>
				<div id="sendcontainer">
				Zum Ausf&uuml;hren den Knopf dr&uuml;cken:<br>
					<input type="submit" name="button1" value="Ausf&uuml;hren" >
				</div>
				<div id="outputcontainer">
					<textarea name="output" cols="110" rows="10"><?php echo htmlentities(runOasys()); ?></textarea>
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
       		<div id="customsearch">
			<gcse:search></gcse:search>
        	</div>
	</div>
	<?php include "piwik.php"; ?>
</body>

</html>

<?php

	function downloadURL() {
		$ranName=str_shuffle($_SESSION['randNum']);
		shell_exec("cd ./uploads/; tar cfz ../downloads/".$ranName.".tgz ./".$_SESSION['randNum'].";");
		return "<span><a href='".$HOSTURL."/downloads/".$ranName.".tgz'>Archiv ".$ranName.".tgz downloaden</a>";
	}
	function runOasys() {
		if(isset($_SESSION['impl_eingabe']) && isset($_SESSION['sign_eingabe']) && isset($_SESSION['cmd'])) {

			$pattern = '~(.+Com.+)|(DEBUG)|(.+Stream.+)|(BasicIO)|(LineFormat)|(Commands)|(.+File.+)|(.+Process.+)|(.+Signal.+)|(.+User.+)|(.+Wait.+)|(.+Unix.+)~sm'; 
			$result = preg_match($pattern, $_SESSION['impl_eingabe'].$_SESSION['sign_eingabe'].$_SESSUION['cmd']);
			if($result) 
				return "Es wurden unerlaubte Strukturen entdeckt.";
			
			$ranFile = md5(time());
			$_SESSION['randNum']=$ranFile;
			$signStr = "SIGNATURE ".$ranFile;
			$implStr = "IMPLEMENTATION ".$ranFile;

			mkdir("./uploads/".$ranFile);

			file_put_contents("./uploads/".$ranFile."/".$ranFile.".sign",$signStr."\n");
			file_put_contents("./uploads/".$ranFile."/".$ranFile.".impl",$implStr."\n");
			file_put_contents("./uploads/".$ranFile."/".$ranFile.".sign",str_replace("\r","\n",$_SESSION['sign_eingabe']),FILE_APPEND);
			file_put_contents("./uploads/".$ranFile."/".$ranFile.".impl",str_replace("\r","\n",$_SESSION['impl_eingabe']),FILE_APPEND);				
			file_put_contents("./uploads/".$ranFile."/".$ranFile.".exec","a ".$ranFile."\nf ".$ranFile.".impl\ne ".$_SESSION['cmd']);

			$tmp=shell_exec("cd ./uploads/".$ranFile."; oasys < ".$ranFile.".exec > ".$ranFile.".log");
			$result=shell_exec("cd ./uploads/".$ranFile."; oasys < ".$ranFile.".exec");
			$result=explode(">e ",$result);
			$result=str_replace($ranFile,"opal",$result[1]);
			$result=str_replace("opal.impl>^D (quit)","",$result);
			return $result;
			} else {
			return utf8_decode("Nicht alle Felder ausgefült.");
		}
	}
?>
