<?php
session_start();
ob_start(); //start output buffering
include 'config.php';
include 'inc/contributors.php';
include 'tools/gc.php';


//Escape all variables

if(!is_dir($TMPDIR)){
	exit("Bitte erstell ein Verzeichnis '".$TMPDIR."' und gib dem Skript Schreibrechte auf dieses");
}else{
	if(!is_dir($TMPDIR.'/downloads')){
		mkdir($TMPDIR.'/downloads/')
	;}
	if(!is_dir($TMPDIR.'/files')){
		mkdir($TMPDIR.'/files/');
	}
	if(!is_dir($TMPDIR.'/uploads')){
		mkdir($TMPDIR.'/uploads/');
	}

}

//Sessionexpiration
if(isset($_SESSION['sessionstart'])){
	$sessionlife = time() - $_SESSION['sessionstart'];
	if($sessionlife > $SESSIONTIMEOUT){
		session_unset();
		session_destroy();
	} else if($sessionlife < $SESSIONTIMEOUT){
		$_SESSION['sessionstart'] = time();
	}
} else {
	$_SESSION['sessionstart'] = time();
}
/* Check if $_SESSION is set, if not initialize them */
if(!isset($_SESSION['runFunction'])) {$_SESSION['runFunction']=""; }
if(!isset($_SESSION['focus'])) {$_SESSION['focus']=0; }
if(!isset($_SESSION['randNum'])) {$_SESSION['randNum']=md5(time().str_shuffle(time()));}
if(!isset($_SESSION['structnr'])) {
	$_SESSION['structnr']=$MINFILES;
	for($i=0;$i<$MINFILES;$i++){
		init($i);
	}
}else{
	foreach($_SESSION['fileName'] as $i => $impl){
		init($i);
	}
}

/* initialize structure names, etc. */
function init($i){
	global $TMPDIR;
	/* If the structure has no name, create one */
	if(!isset($_SESSION['fileName'][$i])) {
		$_SESSION['fileName'][$i]=substr($_SESSION['randNum'],0,4)."datei".$i;
	}else{
		if($_SESSION['fileName'][$i]==""){
			$_SESSION['fileName'][$i]=substr($_SESSION['randNum'],0,4)."datei".$i;
		}
	}
	/* initialize further $_SESSION if necessary */
	if(!isset($_SESSION['implInput'][$i])) {
		$_SESSION['implInput'][$i]="";
	}

	//random filename for upload
	$base = $TMPDIR."/uploads/";
	$ranFile = md5($i.time().str_shuffle(time()));
	//uploaded code set it in impl
	if(isset($_FILES["impl-".$i]["tmp_name"])) {
		move_uploaded_file($_FILES["impl-".$i]["tmp_name"], $base."/"."impl-".$i.$ranFile);
		if(file_exists($base."/"."impl-".$i.$ranFile)){
			$impl=file_get_contents($base."/"."impl-".$i.$ranFile);
			$_SESSION['implInput'][$i]=preg_replace('/IMPLEMENTATION(.+.)\n/',"",$impl);
			preg_match('/IMPLEMENTATION\s*([A-Za-z0-9]*)\s*/',$impl,$matches);
			$_SESSION['fileName'][$i]=$matches[1];
			unlink($base."/"."impl-".$i.$ranFile);
		}
	}

	if(!isset($_SESSION['signInput'][$i])) {
		$_SESSION['signInput'][$i]="";
	}

	//uploaded code set it in impl-0 and sign-0
	if(isset($_FILES["sign-".$i]["tmp_name"])) {
		move_uploaded_file($_FILES["sign-".$i]["tmp_name"], $base."/"."sign-".$i.$ranFile);
		if(file_exists($base."/"."sign-".$i.$ranFile)){
			$_SESSION['signInput'][$i]=preg_replace('/SIGNATURE.+.\n/',"",file_get_contents($base."/"."sign-".$i.$ranFile));
			unlink($base."/"."sign-".$i.$ranFile);
		}
	}
}

//First Visit? --> set cookie
if(!isset($_COOKIE['visited'])){
	setcookie("visited", 1, time() + (86400 * 365)); //86400sec is one day
	$_SESSION['signInput'][0] = $EXAMPLECODE_SIGN;
	$_SESSION['implInput'][0] = $EXAMPLECODE_IMPL;
	$_SESSION['runFunction'] = $EXAMPLECODE_CMD;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<title>WebOpal <?php echo $VERSION ?></title>
	<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<? if(file_exists('js/jquery-ui.min.js')){
	echo '<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>';
	}else{
	echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>';	
	}?>
	<script src="js/ace.js" type="text/javascript" charset="utf-8"></script>
	<? if(file_exists('js/script.min.js')){
	echo '<script src="js/script.min.js" type="text/javascript" charset="utf-8"></script>';
	}else{
	echo '<script src="js/script.js" type="text/javascript" charset="utf-8"></script>';
	}?>
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
		<div id="heading">
			<h1 style="display:inline;">WebOpal <?php echo htmlentities($VERSION); ?>  </h1>   
			<a href="#" name="features" class="dialog">[Features]</a> &middot; <a href="#" name="changelog" class="dialog">[Changelog]</a> &middot; <a href="#" name="help" class="dialog">[Hilfe]</a>
		</div>
		<hr style="margin:0px -10px;"><br>
		<noscript><span class='error'>Bitte aktiviere Javascript, damit WebOpal ordentlich funktioniert. Wir brauchen das f&uuml;r das Akkordion, sowie f&uuml;r die Ajax-Requests zur Auswertung des Opalcodes.</span><br></noscript>
		<a href="#" id="restore_exampl">Hello World!</a>
		<div id="warning" style="display:none;"><br><br><h1 style="display:inline;">Bitte aktiviere Cookies!</h1><span>(was sind <a href="http://de.wikipedia.org/wiki/HTTP-Cookie" target="_blank">Cookies</a>?)</span></div><br><br>
		<form enctype="multipart/form-data" action="index.php" method="POST" id="mainsubmit">
				<input type="button" value="Struktur hinzuf&uuml;gen" id="addStruc" <?php if($_SESSION['structnr']==$MAXFILES) {echo 'disabled="disabled"';} ;?>>
				<div id="accordion">
				<?php
				/* Print Signature & Implementation Areas */
				foreach($_SESSION['fileName'] as $i => $fn){
					echo '
					<h3 class="filename">
					<span style="float:right" class="delStruc">Löschen</span>
					Struktur '.($i+1).'; Name: <input id="name'.$i.'" class="nameInput" name="fileName['.$i.']" value="'.htmlentities($_SESSION['fileName'][$i]).'">
					<input type="hidden" value="'.$i.'" class="num">
					</h3>
					<div class="struccontainer" style="padding:10px;">
						<div class="implcontainer">
							Implementation: <input type="file" name="impl-'.$i.'"><input type="hidden" name="MAX_FILE_SIZE" value="100000"><input type="submit" value="Upload">
							<div class="impl" id="editor-impl-'.$i.'"></div>
							<input type="hidden" class="impl_hidden" value="'.htmlentities($_SESSION['implInput'][$i]).'" name="implInput['.$i.']" >
						</div>
						<div class="signcontainer">
							Signatur: <input type="hidden" name="MAX_FILE_SIZE" value="100000"><input type="file" name="sign-'.$i.'"><input type="submit" value="Upload">
							<div class="sign" id="editor-sign-'.$i.'"></div>
							<input type="hidden" class="sign_hidden" value="'.htmlentities($_SESSION['signInput'][$i]).'" name="signInput['.$i.']" >
		
						</div>
					</div>';
				}
				?>
				</div>
				<input type="text" id="structnr" name="structnr" value="<?php echo htmlentities($_SESSION['structnr']);?>">
				<br>
				<div id="funccontainer">
					Funktionsaufrufe (auch mehrere z.B. "hello;f(x,y)")<br>
					<input name="runFunction" id="runFunction" type="text" size="43" value="<?php echo htmlentities($_SESSION['runFunction']);?>">
				</div>
				<div id="sendcontainer">
					<br>Fokus : <select name="focus" id="focus">
					<?php
					/* Print Signature & Implementation Areas */
					foreach($_SESSION['fileName'] as $i => $fn){
						if($i==$_SESSION['focus']){$selected="selected";}else{$selected="";}
						echo '
						<option value="'.$i.'">'.htmlentities($_SESSION['fileName'][$i]).'</option>';
					}
					?>
					</select>
					<input type="button" name="execute" id="execute" value="Programm ausf&uuml;hren" >
				</div>
			</form>
				<div id="outputcontainer">
					<textarea id="output" name="output" cols="110" rows="10">Ausgabe</textarea>
				</div>
		<div id="download">
			<input type="button" name="download" class="dialog" value="Download als Tarball">
		</div>
		<br>
		Bibliotheca Opalica Suche:
    	<div id="customsearch">
			<div class="gcse-search"></div>
      </div>		
		<div id="github">
			<a href="https://github.com/gehaxelt/PHP-Webopal" id='githublink'>Fork us on GitHub:</a>
			<iframe src="http://ghbtns.com/github-btn.html?user=gehaxelt&amp;repo=PHP-Webopal&amp;type=fork&amp;count=true" frameborder="0" scrolling="NO" width="95" height="20"></iframe>
		</div>
		<div id="contributors">
			WebOpal (c) 2012 by <?php echo echo_contributors(); ?>, <a href="<?php echo htmlentities($IMPRESSUM); ?>">Impressum</a>
		</div>
	</div>
<div id="dialog"></div>
	<?php include "inc/piwik.php"; ?>
</body>

</html>

<?php
	$output = ob_get_clean();
	ignore_user_abort(true);
	set_time_limit(0);
	header("Connection: close");
	header("Content-Length: ".strlen($output));
	echo $output.str_repeat(' ', 1) ."\n\n\n";
	flush(); //script send all data to the browser
	run_gc(false);
?>
