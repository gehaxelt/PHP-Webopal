<?php
include 'config.php';
/* Installs WebOpal on your server */
$filename="config.example.created.php";
$inst = "";
if(isset($_GET['addslashes'])) {
	if($_GET['addslashes'] == "on") {
		$inst = $inst.'<?php'."\n".
		'$HOSTURL=\''.addslashes($_GET['hosturl']).'\';'."\n".
		'$IMPRESSUM=\''.addslashes($_GET['impressum']).'\';'."\n".
		'$MINFILES='.$_GET['minfiles'].';'."\n".
		'$MAXFILES='.$_GET['maxfiles'].';'."\n".
		'$TIMEOUT='.$_GET['timeout'].';'."\n".
		'$TIMEOUTTXT=\''.addslashes($_GET['timeouttxt']).'\';'."\n".
		'$SESSIONTIMEOUT='.$_GET['sessiontimeout'].';'."\n".
		'$EXAMPLECODE_IMPL=\''.addslashes($_GET['examplecode_impl']).'\';'."\n".
		'$EXAMPLECODE_SIGN=\''.addslashes($_GET['examplecode_sign']).'\';'."\n".
		'$EXAMPLECODE_CMD=\''.addslashes($_GET['examplecode_cmd']).'\';'."\n".
		'$VERSION=\''.addslashes($_GET['version']).'\';'."\n".
		'$ADVERTCOMMENT=\''.addslashes($_GET['advertcomment']).'\';'."\n".
		'?>';
	}
} else {
	$inst = $inst.'<?php'."\n".
	'$HOSTURL=\''.$_GET['hosturl'].'\';'."\n".
	'$IMPRESSUM=\''.$_GET['impressum'].'\';'."\n".
	'$MINFILES='.$_GET['minfiles'].';'."\n".
	'$MAXFILES='.$_GET['maxfiles'].';'."\n".
	'$TIMEOUT='.$_GET['timeout'].';'."\n".
	'$TIMEOUTTXT=\''.$_GET['timeouttxt'].'\';'."\n".
	'$SESSIONTIMEOUT='.$_GET['sessiontimeout'].';'."\n".
	'$EXAMPLECODE_IMPL=\''.$_GET['examplecode_impl'].'\';'."\n".
	'$EXAMPLECODE_SIGN=\''.$_GET['examplecode_sign'].'\';'."\n".
	'$EXAMPLECODE_CMD=\''.$_GET['examplecode_cmd'].'\';'."\n".
	'$VERSION=\''.$_GET['version'].'\';'."\n".
	'$ADVERTCOMMENT=\''.$_GET['advertcomment'].'\';'."\n".
	'$TMPDIR=\''.$_GET['tmpdir'].'\';'."\n".
	'?>';
}

file_put_contents($filename,$inst);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>WebOpal <? echo $VERSION ?> Installer</title>
</head>
<body>
<h1>WebOpal <? echo $VERSION ?> Installer</h1>
<form action="install.php" method="GET"><? //using GET because someone wants to set it up via script ?>
Hosturl: <input type="text" name="hosturl" /><br>
Impressumurl: <input type="text" name="impressum" /><br>
Minfiles: <input type="text" name="minfiles" value="2" /><br>
Maxfiles: <input type="text" name="maxfiles" value="3" /><br>
Timeout: <input type="text" name="timeout" value="10" /><br>
Timeout text: <input type="text" name="timeouttxt" value="Your program maybe timed out (>'.$TIMEOUT.'s)" /><br>
Sessiontimeout: <input type="text" name="sessiontimeout" value="600" /><br>
Examplecode-IMPL: <input type="text" name="examplecode_impl" value="DEF hello == 'Hello World!'" /><br>
Examplecode-SIGN: <input type="text" name="examplecode_sign" value="FUN hello : denotation" /><br>
Examplecode-CMD: <input type="text" name="examplecode_cmd" value="hello" /><br>
Version: <input type="text" name="version" value="v0.2" /><br>
Advertcomment: <input type="text" name="advertcomment" value="-- created with WebOpal '.$VERSION.' (https://github.com/gehaxelt/PHP-Webopal) on Server '.$HOSTURL" /><br>
Tempdir: <input type="text" name="tmpdir" value="tmp" /><br>
Add slashes? <input type="checkbox" name="addslashes" value="on" />
<input type="submit" value="Install" />
</form>
</body>
</html>
