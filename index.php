<?php
session_start();
ob_start(); //start output buffering

include_once 'config.php';
include_once 'inc/contributors.php';
include_once 'tools/gc.php';
include_once 'tools/mobiledetect.php';

$VERSION="0.5&alpha;";

//show mobile site
$detect = new Mobile_Detect();
if($detect->isMobile()){
	include 'mobile/mobilepage.php';
	exit();
}

//Escape all variables

//check if the standard dirs has been created
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
	if(!is_dir($TMPDIR.'/userfiles')){
		mkdir($TMPDIR.'/userfiles/');
	}
	if(!is_file($TMPDIR.'/users')){
		file_put_contents($TMPDIR.'/users',serialize(Array()));
	}
}

// As as the multiuser is not working completely, Create a Testuser test with password 1234
file_put_contents($TMPDIR.'/users',serialize(Array("test"=>Array("email"=>"test@de.de","pw"=>sha1("1234"),"lasttime"=>time(),"path"=>"test"))));
if(!is_dir($TMPDIR.'/userfiles/test')){
	mkdir($TMPDIR.'/userfiles/test/');
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
if(!isset($_SESSION['actTab'])) {$_SESSION['actTab']=0; }
if(!isset($_SESSION['debug'])) {$_SESSION['debug']=false; }
if(!isset($_SESSION['randNum'])) {$_SESSION['randNum']=md5(time().str_shuffle(time()));}
if(!isset($_SESSION['loggedIn'])) {$_SESSION['loggedIn']=false; }
if(!isset($_SESSION['loggedInAs'])) {$_SESSION['loggedInAs']=""; }
if(!isset($_SESSION['loggedInPath'])) {$_SESSION['loggedInPath']=""; }

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
			$_SESSION['implInput'][$i]=$impl;
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
			$_SESSION['signInput'][$i]=file_get_contents($base."/"."sign-".$i.$ranFile);
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
$showChangeLog='';
if(!isset($_COOKIE['version'])){
	$showChangeLog='firstTime';
	setcookie("version", $VERSION, time() + (86400 * 365)); //86400sec is one day
}else{
	if($_COOKIE['version']!=$VERSION){
	$showChangeLog='updateSince'.$_COOKIE['version'];
	setcookie("version", $VERSION, time() + (86400 * 365)); //86400sec is one day
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="de">
  <head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
  <title>WebOpal <?php echo $VERSION; ?></title>
  <script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
  <link rel="shortcut icon" href="favicon.ico">
	<?if(file_exists('css/style.css')){
		echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
	}else{
		echo '<link rel="stylesheet" type="text/css" href="css/style.fallback.css">';
	}
	if(file_exists('js/jquery-ui.min.js')){
		echo '<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>';
	}else{
		echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>';	
	}
	?>

	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/ace.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/sha.min.js" charset="utf-8"></script>

	<? if(file_exists('js/script.min.js')){
		echo '<script src="js/script.min.js" type="text/javascript" charset="utf-8"></script>';
	}else{
		echo '<script src="js/functions.js" type="text/javascript" charset="utf-8"></script>';
		echo '<script src="js/script.js" type="text/javascript" charset="utf-8"></script>';
	}
	?>
	<?php if($BUGREPORT){
		echo 
		'<script language="javascript" type="text/javascript">
		function showRecaptcha(element) {
			Recaptcha.create("'.$PUBLICKEY.'?>", element, {
				theme: "red",
				callback: Recaptcha.focus_response_field
			});
		}
		</script>'
	;}?>
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
    <?php /* move to main form? */ ?>
    <div><input type="text" id="autocomplete"></div>
    <div id="wrapper">
      <div id="heading">
        <a href="<?php echo $HOSTURL ?>"><img src="img/logo.png" id="logo" /></a><h1 style="display:inline;">WebOpal <abbr title="Version <?php echo $VERSION; ?>">v<?php echo $VERSION; ?></abbr></h1>   
			<a href="#" name="features" class="dialog">[Features]</a> &middot; <a href="#" name="changelog" class="dialog">[Changelog]</a> &middot; <a href="#" name="help" class="dialog">[Hilfe]</a> 
			<?php if($BUGREPORT){ echo '&middot; <a href="#" id="bugReport">[Bug- & Ideenreport]</a>';}?>
			<?php if($_SESSION['loggedIn']){
				$show="style='display:none;'";
				echo '<a href="#" class="floatR" id="logout">[Logout]</a>';
			} else {
				$show="";
				echo '<a href="#" class="floatR" id="login">[Login]</a>';
			}?>
		</div>
		<?php if($_SESSION['loggedIn']){ echo "Hallo ".$_SESSION['loggedInAs']."!";} ?>
		<hr style="margin:0px -10px;"><br>
		<noscript>
			<span class='error'>Bitte aktiviere Javascript, damit WebOpal ordentlich funktioniert. Wir brauchen das f&uuml;r das Akkordion, sowie f&uuml;r die Ajax-Requests zur Auswertung des Opalcodes.</span><br>
		</noscript>
		<div class="codeRelated" <?php echo $show;?>><p>Wenn Du gerade keine Idee hast, beginne doch mit einer <a href="#" id="restore_exampl">Hello World!</a>-Struktur.</p>
		<input type="button" value="Struktur hinzuf&uuml;gen" id="addStruc" <?php if($_SESSION['structnr']==$MAXFILES) {echo 'disabled="disabled"';} ;?>></div><br>
		<?php if($_SESSION['loggedIn']){
			$dirs="";$dircount=0;
			foreach (new DirectoryIterator($TMPDIR.'/userfiles/'.$_SESSION['loggedInPath']) as $fn) {
				 if (!$fn->isDot()) {
				  $dirs.='<a href="#" class="changeDir" name="';
				  $dirs.=$fn->getFilename();
				  $dirs.='">';
				  $dirs.=$fn->getFilename();
				  $dirs.='</a><br>';
				  $dircount++;
				 }
			 }
		echo "Du bist gerade in keinem Verzeichnis. Wähle eins:<!--<a href='#' id='getFolders'>[Verzeichnis wechseln]</a>-->
		<div id='folders'>$dirs</div>";} ?>
		<div id="warning" style="display:none;"><br><br>
			<h1 style="display:inline;">Bitte aktiviere Cookies!</h1>
			<span>(was sind <a href="http://de.wikipedia.org/wiki/HTTP-Cookie" target="_blank">Cookies</a>?)</span>
		</div>
		<div class="codeRelated" <?php echo $show;?>>
			<br>
			<br><input type="button" id="pseudo">
			<form enctype="multipart/form-data" action="index.php" method="POST" id="mainsubmit">
					<div id="accordion">
					<?php
					/* Print Signature & Implementation Areas */
					foreach($_SESSION['fileName'] as $i => $fn){
						echo '
						<h3 class="filename">
						<span style="float:right" class="delStruc">Löschen</span>
						Struktur <input id="name'.$i.'" class="nameInput" name="fileName['.$i.']" value="'.htmlentities($_SESSION['fileName'][$i]).'">
						<input type="hidden" value="'.$i.'" class="num">
						</h3>
						<div class="struccontainer" style="padding:10px;">
							<div class="implcontainer resizeEditor">
								<div class="resizeNot">Implementation: <input type="file" name="impl-'.$i.'"><input type="hidden" name="MAX_FILE_SIZE" value="100000"><input type="submit" value="Upload"></div>
								<div class="impl resizeAlso" id="editor-impl-'.$i.'"></div>
								<input type="hidden" class="impl_hidden" value="'.htmlentities($_SESSION['implInput'][$i],ENT_QUOTES,'UTF-8').'" name="implInput['.$i.']" >
							</div>
							<div class="signcontainer resizeEditor">
								<div class="resizeNot">Signatur: <input type="hidden" name="MAX_FILE_SIZE" value="100000"><input type="file" name="sign-'.$i.'"><input type="submit" value="Upload"></div>
								<div class="sign resizeAlso" id="editor-sign-'.$i.'"></div>
								<input type="hidden" class="sign_hidden" value="'.htmlentities($_SESSION['signInput'][$i],ENT_QUOTES,'UTF-8').'" name="signInput['.$i.']" >
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
						<input name="debug" id="debug" type="checkbox" value="1"> Debugmodus 
						<input type="button" name="execute" id="execute" value="Programm ausf&uuml;hren" >
					</div>
					<input type="hidden" id="actTab" name="actTab" value="<?php echo htmlentities($_SESSION['actTab']);?>">
				</form>
					<div id="outputcontainer">
						<div id="output" name="output">Ausgabe</div>
					</div>
			<div id="download">
				<input type="button" name="download" class="dialog" value="Download als Tarball">
			</div>
		<br>
		Bibliotheca Opalica Suche:
    		<div id="customsearch">
			<div class="gcse-search"></div>
      		</div>
		</div>
		<br>
		<div id="github">
			<a href="https://github.com/gehaxelt/PHP-Webopal" id='githublink'>Fork us on GitHub:</a>
			<iframe src="http://ghbtns.com/github-btn.html?user=gehaxelt&amp;repo=PHP-Webopal&amp;type=fork&amp;count=true" frameborder="0" scrolling="NO" width="95" height="20"></iframe>
		</div>
      <div id="contributors">
      	WebOpal (c) 2012 by <?php echo echo_contributors(); ?>, <a href="<?php echo htmlentities($IMPRESSUM); ?>">Impressum</a>
      </div>
    </div>
    <?php /* For jQuery UI Dialogs: */ ?>	
    <div id="dialog"></div>
    <?php /* For Javascript Variables */ ?>
    <div id="forJavascript">
      <input type="hidden" id="timeOut" value="<?php echo $SESSIONTIMEOUT*1000;?>">
      <input type="hidden" id="maxStruc" value="<?php echo $MAXFILES;?>">
      <input type="hidden" id="implEx" value='<?php echo $EXAMPLECODE_IMPL;?>'>
      <input type="hidden" id="signEx" value="<?php echo $EXAMPLECODE_SIGN;?>">
      <input type="hidden" id="cmdEx" value="<?php echo $EXAMPLECODE_CMD;?>">
      <input type="hidden" id="showChangeLog" value="<?php echo $showChangeLog;?>">
      <input type="hidden" id="strucPre" value="<?php echo substr($_SESSION['randNum'],0,4);?>">
      <input type="hidden" id="secret" value="<?php echo $_SESSION['randNum'];?>">
    </div>
    <?php include "inc/piwik.php"; ?>
  </body>
</html>

<?php
	//Output buffering for parallel gargabe collection and other things
	$output = ob_get_clean();
	ignore_user_abort(true);
	set_time_limit(0);
	header("Connection: close");
	header("Content-Length: ".strlen($output));
	echo $output.str_repeat(' ', 1) ."\n\n\n";
	flush(); //script send all data to the browser
	run_gc(false);
?>
