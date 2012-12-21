<?php
	if(file_exists("config.php")){
		include 'config.php';
	} else if(file_exists("../config.php")){
		include '../config.php';
	}
?>
<!DOCTYPE html> 
<html> 
<head> 
	<title>WebOpal v<?php echo $VERSION; ?></title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
	<?php if(file_exists('js/jquery-1.8.3.min.js')){
		echo '<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>';
	}else{
		echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>';	
	}
	?>
	<?php if(file_exists('js/jquery.mobile-1.2.0.min.js')){
		echo '<script type="text/javascript" src="js/jquery.mobile-1.2.0.min.js"></script>';
	}else{
		echo '<script type="text/javascript" src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>';	
	}
	?>
</head> 
<body> 

<div data-role="page" id="editor">

	<div data-role="header">
		<h1>WebOpal v<?php echo $VERSION; ?></h1>
		<div data-role="navbar">
			<ul>
				<li><a href="#editor" data-transition="pop">Editor</a></li>
				<li><a href="#features" data-transition="pop">Features</a></li>
				<li><a href="#changelog" data-transition="pop">Changelog</a></li>
				<li><a href="#help" data-transition="pop">Hilfe</a></li>
			</ul>
		</div>
	</div>
	
	<div data-role="content">
		<p>Editor</p>		
	</div>
</div>
<div data-role="page" id="features">

	<div data-role="header">
		<h1>WebOpal v<?php echo $VERSION; ?></h1>
		<div data-role="navbar">
			<ul>
				<li><a href="#editor" data-transition="pop">Editor</a></li>
				<li><a href="#features" data-transition="pop">Features</a></li>
				<li><a href="#changelog" data-transition="pop">Changelog</a></li>
				<li><a href="#help" data-transition="pop">Hilfe</a></li>
			</ul>
		</div>
	</div>
	
	<div data-role="content">
		<p>Features</p>		
	</div>
</div>
<div data-role="page" id="changelog">

	<div data-role="header">
		<h1>WebOpal v<?php echo $VERSION; ?></h1>
		<div data-role="navbar">
			<ul>
				<li><a href="#editor" data-transition="pop">Editor</a></li>
				<li><a href="#features" data-transition="pop">Features</a></li>
				<li><a href="#changelog" data-transition="pop">Changelog</a></li>
				<li><a href="#help" data-transition="pop">Hilfe</a></li>
			</ul>
		</div>
	</div>
	
	<div data-role="content">
		<p>Changelog</p>		
	</div>
</div>
<div data-role="page" id="help">

	<div data-role="header">
		<h1>WebOpal v<?php echo $VERSION; ?></h1>
		<div data-role="navbar">
			<ul>
				<li><a href="#editor" data-transition="pop">Editor</a></li>
				<li><a href="#features" data-transition="pop">Features</a></li>
				<li><a href="#changelog" data-transition="pop">Changelog</a></li>
				<li><a href="#help" data-transition="pop">Hilfe</a></li>
			</ul>
		</div>
	</div>
	
	<div data-role="content">
		<p>Help</p>		
	</div>
</div>

</body>
</html>
