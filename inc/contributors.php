<?php
//simple contributors list with adaptive punctation
$contributors = array();
$contributors[] = "Sebastian Neef";
$contributors[] = "Tim M&uuml;ller";
$contributors[] = "Lukas Eipert";
$contributors[] = "Moritz Lummerzheim";
$contributors[] = "Opalion (aka Kaufi)";

function echo_contributors(){
	global $contributors;
	$last = array_pop($contributors);
	if(count($contributors)>1){
		return implode($contributors,", ")." & ".$last;
	} else {
		return $last;
	}
}
?>
