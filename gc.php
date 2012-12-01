<?php
include 'config.php';
	$dirs = scandir("./uploads");
	$delfiles = 0;
	foreach($dirs as $entry){
		if($entry === '.' or $entry === '..') continue;
		$dir = "./uploads/".$entry;
		if(is_dir($dir)){
			$timestamp = "";
			$filename = $dir."/time.stamp";
			$file = fopen($filename, "r+");
			$time = fgets($file);
			fclose($file);
			if((time() - intval($time)) > $SESSIONTIMEOUT){
				rrmdir($dir);
				$delfiles++;
				echo("Deleted folder: ".$dir."<br />");
			}
		}
	}
	echo($delfiles." folder(s) deleted");
	echo("<br />");
	$files = scandir("./downloads");
	$delfiles = 0;
	foreach($files as $entry){
		if($entry === '.' or $entry === '..') continue;
		$filename = "./downloads/".$entry;
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
				echo("Deleted file: ".$fname."<br />");
			}
		}
	}
	echo($delfiles." file(s) deleted");

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
