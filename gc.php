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
				echo("Deleted: ".$dir."<br />");
			}
		}
	}
	echo($delfiles." file(s) deleted");

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
