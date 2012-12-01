<?php

$contributors = array();
$contributors[] = "Sebastian Neef";
$contributors[] = "Tim M&uuml;ller";
$contributors[] = "Lukas Eipert";
$contributors[] = "Moritz Lummerzheim";

function echo_contributors(){
	global $contributors;
	$count = count($contributors);
	if($count==1){
		echo($contributors[0]);
	} else if ($count>=2){
		for($i=0;$i<$count;$i++){
			echo($contributors[$i]);
			if($i<$count-2){
				echo(", ");
			}
			if($i==$count-2){
				echo(" & ");
			}
		}
	}
}
?>
