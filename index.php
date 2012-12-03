<?php
session_start();
ob_start(); //start output buffering
include 'config.php';
include 'contributors.php';
include 'gc.php';

//Sessionexpiration
if(isset($_SESSION['sessionstart'])){
	$sessionlife = time() - $_SESSION['sessionstart'];
	if($sessionlife > $SESSIONTIMEOUT){
		session_unset();
		session_destroy();
	}
} else {
	$_SESSION['sessionstart'] = time();
}

/* Check if $_SESSION is set, if not initialize them */
if(!isset($_SESSION['cmd'])) {$_SESSION['cmd']=""; }
if(!isset($_SESSION['focus'])) {$_SESSION['focus']=0; }
if(!isset($_SESSION['randNum'])) {$_SESSION['randNum']=md5(time().str_shuffle(time()));}
if(!isset($_SESSION['structnr'])) {$_SESSION['structnr']=$MINFILES;}

/* Check if $_POST is set, if yes, update $_SESSION */
if(isset($_POST['structnr'])) {
	if(intval($_POST['structnr'])>$MAXFILES){	
		$_SESSION['structnr']=$MINFILES;
	} else {
		$_SESSION['structnr']=intval($_POST['structnr']);
	}
}

for($i=0;$i<$_SESSION['structnr'];$i++){
	/* initialize further $_SESSION if necessary */
	if(!isset($_SESSION['name'][$i])) {
		$_SESSION["name"][$i]=substr($_SESSION['randNum'],0,4)."datei".$i;
	}else{
		if($_SESSION["name"][$i]==""){
			$_SESSION["name"][$i]=substr($_SESSION['randNum'],0,4)."datei".$i;
		}
	}
	if(!isset($_SESSION['impl_eingabe'][$i])) {$_SESSION['impl_eingabe'][$i]=""; }
	if(!isset($_SESSION['sign_eingabe'][$i])) {$_SESSION['sign_eingabe'][$i]=""; }

	/* If the structure has no name, create one */
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<title>WebOpal v0.1a</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
	<script src="ace/ace.js" type="text/javascript" charset="utf-8"></script>
	<script language="javascript" type="text/javascript">
	var editors = new Array();
    	$(function() {
		$("#accordion").accordion({
			collapsible:false,heightStyle: "content", event: "mouseup",active : <?php echo $_SESSION['focus'];?>,
			activate: function(event, ui){
				s = ui.newPanel.find(".impl").attr("id");
				editors[s].focus();
				//editors[s].gotoLine(editors[s].session.getLength());				
			}
		});
		$(".struccontainer").each(function(index){
			impl = $(this).find(".impl").attr("id");
			sign = $(this).find(".sign").attr("id");
			editors[impl] = ace.edit(impl);
			editors[impl].setTheme("ace/theme/chrome");
			editors[impl].getSession().setMode("ace/mode-opal");
			editors[impl].getSession().setValue($(this).find(".impl_hidden").val());
			editors[sign] = ace.edit(sign);
			editors[sign].setTheme("ace/theme/chrome");
			editors[sign].getSession().setMode("ace/mode-opal");
			editors[sign].getSession().setValue($(this).find(".sign_hidden").val());
		});
		$("#button1").click(function(){
			$(".struccontainer").each(function(index){
				$(this).find(".impl_hidden").val(editors[$(this).find(".impl").attr("id")].getSession().getValue())
				$(this).find(".sign_hidden").val(editors[$(this).find(".sign").attr("id")].getSession().getValue())
			});
		    $.get( 'oasys.php', $('#mainsubmit').serialize(), function(data) {
			$('#output').text(data)
		       },
		       'json' // I expect a JSON response
		    );
		});
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
		<h1>WebOpal v0.1a <? echo time();?></h1>
		<span>Bitte in der Impl bzw. Sign die IMPLEMENTATION bzw. SIGNATURE weglassen. </span>
		<?php
		//First Visit? --> set cookie
		/*if(!isset($_COOKIE['visited'])){
			setcookie("visited", 1, time() + (86400 * 365)); //86400sec is one day
			$_SESSION['sign_eingabe'][0] = $EXAMPLECODE_SIGN;
			$_SESSION['impl_eingabe'][0] = $EXAMPLECODE_IMPL;
			$_SESSION['cmd'] = "hello";
		}*/
		//Cookietest
		if(count($_COOKIE) == 0){
					echo("<h1>Bitte aktiviere Cookies!</h1> (was sind <a href=\"http://de.wikipedia.org/wiki/HTTP-Cookie\" target=\"_blank\">Cookies</a>?)");
		}
		?>
		<form action="index.php" method="POST"><input type="text" name="structnr" value="<?php echo($_SESSION['structnr']); ?>" /><input type="submit" value="Anzahl der Strukturen &auml;ndern" /></form>
		<form action="index.php" method="post" id="mainsubmit">
				<div id="accordion">
				<?php
				/* Print Signature & Implementation Areas */
				for($i=0;$i<$_SESSION['structnr'];$i++){
					if($i==$_SESSION['focus']){$checked="checked";}else{$checked="";}
					echo '
					<h3 class="filename">
						Struktur '.($i+1).'; Name: <input id="name'.$i.'" class="name_eingabe" name="name['.$i.']" value="'.htmlentities($_SESSION['name'][$i]).'">
						<input type="radio" name="focus" value="'.$i.'" '.$checked.'> Fokus
					</h3>
					<div class="struccontainer" style="padding:10px;">
					<div class="implcontainer">
	    					Eingabe f&uuml;r den Implementationsteil:
						<div class="impl" id="editor-impl-'.$i.'"></div>
						<input type="hidden" class="impl_hidden" value="'.htmlentities($_SESSION['impl_eingabe'][$i]).'" name="impl_eingabe['.$i.']" ></input>
	    				</div>
					<div class="signcontainer">
						Eingabe f&uuml;r den Signaturteil:
						<div class="sign" id="editor-sign-'.$i.'"></div>
						<input type="hidden" class="sign_hidden" value="'.htmlentities($_SESSION['sign_eingabe'][$i]).'" name="sign_eingabe['.$i.']" ></input>
	    				</div>';
					echo('</div>');
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
					<input type="button" name="button1" id="button1" value="Ausf&uuml;hren" >
				</div>
			</form>
				<div id="outputcontainer">
					<textarea id="output" name="output" cols="110" rows="10"><?php
							echo htmlentities(runOasys($_SESSION['impl_eingabe'],$_SESSION['sign_eingabe'],$_SESSION['cmd'],$_SESSION['name'],$_SESSION['focus']));
										?>
					</textarea>
				</div>
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
		<div id="github"><a href="https://github.com/gehaxelt/PHP-Webopal" id='githublink'>Fork us on GitHub:</a>
			<iframe src="http://ghbtns.com/github-btn.html?user=gehaxelt&repo=PHP-Webopal&type=fork&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="95" height="20"></iframe></div>
	<div id="contributors">
		WebOpal (c) 2012 by <?php echo_contributors(); ?>, 
		<a href="<?php echo htmlentities($IMPRESSUM); ?>">Impressum</a>
	</div>
	<br />
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
	return "";
	}
	$output = ob_get_clean();
	ignore_user_abort(true);
	set_time_limit(0);
	header("Connection: close");
	header("Content-Length: ".strlen($output));
	header("Content-Encoding: none");
	echo $output.str_repeat(' ', 1) ."\n\n\n";
	flush(); //script send all data to the browser

	run_gc(false);
?>
