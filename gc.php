<?php
include 'config.php';

if(isset($_GET['output'])){
	if($_GET['output'] == "true"){
		run_gc(true);
	} else {
		run_gc(false);
	}
}

function run_gc($output=false){
	global $SESSIONTIMEOUT;
	$dirs = scandir("./tmp/uploads");
	$delfiles = 0;
	foreach($dirs as $entry){
		if($entry === '.' or $entry === '..') continue;
		$dir = "./tmp/uploads/".$entry;
		if(is_dir($dir)){
			$timestamp = "";
			$filename = $dir."/time.stamp";
			$file = fopen($filename, "r+");
			$time = fgets($file);
			fclose($file);
			if((time() - intval($time)) > $SESSIONTIMEOUT){
				rrmdir($dir);
				$delfiles++;
				if($output) echo("Deleted folder: ".$dir."<br />");
			}
		}
	}
	if($output){
		echo($delfiles." folder(s) deleted");
		echo("<br />");
	}
	$files = scandir("./tmp/downloads");
	$delfiles = 0;
	foreach($files as $entry){
		if($entry === '.' or $entry === '..') continue;
		$filename = "./tmp/downloads/".$entry;
		if(is_dir($filename)) continue;
		if(endsWith($filename,"stamp")){
			$timestamp = "";
			$file = fopen($filename, "r+");
			$time = fgets($file);
			fclose($file);
			if((time() - intval($time)) > $SESSIONTIMEOUT){
				$fname = str_replace(".stamp", "", $filename).".tgz";
				if(file_exists($fname)){
					unlink($fname);
				}
				if(file_exists($filename)){
					unlink($filename);
				}
				$delfiles++;
				if($output) echo("Deleted file: ".$fname."<br />");
			}
		}
	}
	if($output) echo($delfiles." file(s) deleted");
}

function endsWith( $str, $sub ) {
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
?>
