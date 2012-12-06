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
	$base = "tmp";
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
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
	<script src="ace/ace.js" type="text/javascript" charset="utf-8"></script>
	<script language="javascript" type="text/javascript">
	/* Array for all the ACE editors */
	var editors = new Array();
	var currentStruc = <?php echo $_SESSION['structnr']; ?>;
	var maxStruc = <?php echo $MAXFILES; ?>;
	var strucPre = "<?php echo substr($_SESSION['randNum'],0,4); ?>";
	/* Execute if DOM is ready */
   $(function() {
   	
   	/* initialize Accordion */
		$("#accordion").accordion({
			collapsible:false,
			heightStyle: "content",
			event: "mouseup",
			active : <?php echo $_SESSION['focus'];?>,
			activate: function(event, ui){
				s = ui.newPanel.find(".impl").attr("id");
				editors[s].focus();
			}
		});
		
		/* initialize ACE enviroments */
		$(".struccontainer").each(function(index){
			impl = $(this).find(".impl").attr("id");
			sign = $(this).find(".sign").attr("id");
			editors[impl] = ace.edit(impl);
			editors[impl].setTheme("ace/theme/chrome");
			editors[impl].getSession().setMode("ace/mode/opal");
			editors[impl].getSession().setValue($(this).find(".impl_hidden").val());
			editors[sign] = ace.edit(sign);
			editors[sign].setTheme("ace/theme/chrome");
			editors[sign].getSession().setMode("ace/mode/opal");
			editors[sign].getSession().setValue($(this).find(".sign_hidden").val());
		});

		$("#restore_exampl").click(function(){
			num=$('.num:first').val();
			editors["editor-impl-"+num].setValue('<?php echo $EXAMPLECODE_IMPL;?>');
			editors["editor-sign-"+num].setValue('<?php echo $EXAMPLECODE_SIGN;?>');
			$('#runFunction').val('<?php echo $EXAMPLECODE_CMD;?>');
			//$('.focus:first').attr("checked","checked");
		});

		$(document).on("change",'.nameInput',function(event){
			num=$(this).parent().find('.num').val();
			name=$(this).val();
			$('#focus option:eq('+num+')').html(name);
		});

		$(document).on("click",'.delStruc',function(event){
			if($('.delStruc').size()>1){
				name=$(this).parent().find('.nameInput').val();
				var answer = confirm (name+" wirklich löschen?")
				if(answer){
					num=$(this).parent().find('.num').val();
					$('.filename:eq('+num+')').remove();
					$('.struccontainer:eq('+num+')').remove();
					$('#focus option:eq('+num+')').remove();
					currentStruc--;
					if(currentStruc<maxStruc){$("#addStruc").removeAttr("disabled");}
					$('#structnr').val(currentStruc);
					if($('.delStruc').size()<=1){$('.delStruc').hide();}
					$('#accordion').accordion( "option", "active", num-1);
					$.get(
							'ajax.php',
							"page=update&structnr="+currentStruc+"&delete="+num,
							function() {},
							'json'
					);
				}
			}
		});

		$('#addStruc').click(function(){
				currentStruc++;
				strucNum=parseInt($('.num:last').val())+1;
				name= strucPre+"datei"+strucNum
				$('#accordion').append(
					'<h3 class="filename">'+
					'	<span style="float:right" class="delStruc" v>Löschen</span>'+
					'	Struktur '+currentStruc+'; Name: <input id="name'+strucNum+'" class="nameInput" name="fileName['+strucNum+']" value="'+name+'">'+
					'	<input type="hidden" value="'+strucNum+'" class="num">'+
					'</h3>'+
					'<div class="struccontainer" style="padding:10px;">'+
					'	<div class="implcontainer">'+
					'		Implementation: <input type="file" name="impl-'+strucNum+'"><input type="hidden" name="MAX_FILE_SIZE" value="100000" ><input type="submit" value="Upload">'+
					'		<div class="impl" id="editor-impl-'+strucNum+'"></div>'+
					'		<input type="hidden" class="impl_hidden" value="" name="implInput['+strucNum+']" >'+
					'	</div>'+
					'	<div class="signcontainer">'+
					'		Signatur: <input type="hidden" name="MAX_FILE_SIZE" value="100000" ><input type="file" name="sign-'+strucNum+'" ><input type="submit" value="Upload">'+
					'		<div class="sign" id="editor-sign-'+strucNum+'"></div>'+
					'		<input type="hidden" class="sign_hidden" value="" name="signInput['+strucNum+']" >'+
					'	</div>'+
					'</div>'
				).accordion('destroy').accordion();
				impl = "editor-impl-"+strucNum;
				sign = "editor-sign-"+strucNum;
				editors[impl] = ace.edit(impl);
				editors[impl].setTheme("ace/theme/chrome");
				editors[impl].getSession().setMode("ace/mode/opal");
				editors[sign] = ace.edit(sign);
				editors[sign].setTheme("ace/theme/chrome");
				editors[sign].getSession().setMode("ace/mode/opal");
				$('#focus').append('<option value="'+strucNum+'">'+name+'</option>');
				$('#structnr').val(currentStruc);
				if($('.delStruc').size()>1){$('.delStruc').show();}
				$.get(
					'ajax.php',
					"page=update&file="+strucNum+"&structnr="+currentStruc,
					function() {},
					'json'
				);
			if(currentStruc==maxStruc){
    			$("#addStruc").attr("disabled","disabled")
    		}
		});
		
		/* Bind click action to execute button */
		$("#execute").click(function(){
			
			/* copy content of ACE to hidden inputs */
			$(".struccontainer").each(function(index){
				$(this).find(".impl_hidden").val(editors[$(this).find(".impl").attr("id")].getSession().getValue())
				$(this).find(".sign_hidden").val(editors[$(this).find(".sign").attr("id")].getSession().getValue())
			});
			
			/* Deactivate Button */
			$("#execute").attr("disabled","disabled")
			$("#execute").attr("value","Lade...")
			
			/* GET Request */
			$.get(
				'oasys.php', 
				$('#mainsubmit').serialize(),
				/* Populate output and activate button on success */
				function(data) {
					$('#output').text(data)
					$("#execute").attr("value","Programm ausführen")
					$("#execute").removeAttr("disabled")
				},
				'json'
			);
		});

		/* Bind click functions for download, changelog, etc  */
		$(".dialog").click(function(){
			name=$(this).attr("name");
			w=700;
			if(name=="download"){
				/* Execute OPAL Code */
				$('#execute').click();
				w=300;
			}
			$.get(
				'ajax.php',
				"page="+name,
				function(data) {
					$('#dialog').html(data.text);
					$('#dialog').html(data.text);
					$('#dialog').dialog({title: data.title, width: w});
				},
				'json'
			);
		});

		$('#runFunction').keypress(function(e){
			if (e.which == 13) {
				e.preventDefault();
				$("#execute").click();
			}
		});

		/* Bind action for ctrl+space code completion */
		$(document).keydown(function(e){
			if( (e.ctrlKey||e.metaKey) && String.fromCharCode(e.charCode || e.keyCode) === " "){
				
				//Find focused ACE editor
				s=$('.ace_focus').attr("id");
				try { editors[s]; }
				catch (e) {return false;}

				//Get word left from cursor
				editors[s].selection.selectWordLeft();
				var wordAtLeft = editors[s].session.getDocument().getTextRange(editors[s].selection.getRange())
				editors[s].selection.selectWordRight();

				// If wordAtLeft too small, dont try to complete
				if ( wordAtLeft.length < 2 ){ return false; }

				//List of words which should be always available for code completion
				var possibleWords = new Array("IMPORT","denotation","Denotation","COMPLETELY","ONLY","NatConv","RealConv","CharConv","WHERE", "newline");

				/* Extend the possibleWords List with words longer than 4 letters in ace editors
				 * If you have for example the word "sortYear" in one of the editors
				 * and type "sort"+ctrl+space in another editor, it should autocomplete
				 */
				$(".ace_editor").each(function(index){
					id=$(this).attr("id");
					var inEditor = editors[id].getValue().match(/((?=\.)?\$?_?[A-Za-z_]{4,})/g);
					if(inEditor!=null){
						for(i=0;i<inEditor.length;i++){
							if(possibleWords.indexOf(inEditor[i])==-1){possibleWords.push(inEditor[i]);}
						}
					}
				});

				var foundWords = new Array();

				/* Check if our wordLeft has ONE possible match in possibleWords */
				for(i=0;i<possibleWords.length;i++){
					var possibleWord = possibleWords[i];
					if (	possibleWord !== undefined &&
							possibleWord !== wordAtLeft &&
							possibleWord.substring(0, wordAtLeft.length) === wordAtLeft &&
							possibleWord !== 'length') {
								// stop, if there is more than one possibility
								if ( foundWords.length === 1 ){ return false; }
								if ( possibleWord !== 'length'  ){ foundWords[ 0 ] = possibleWord; }
					}
				}
				
				// stop, if no word found
				if ( foundWords.length === 0 ) return false;

				// insert found word
				editors[s].removeWordLeft();
				editors[s].insert( foundWords[ 0 ] );

				return false;
			}
		});
		
		/* Print warning if cookies are disabled */
		if (navigator.cookieEnabled != true) {
		  $('#warning').show()
		}
		if($('.delStruc').size()<=1){
		$('.delStruc').hide();
		}
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
		<div id="heading">
			<h1 style="display:inline;">WebOpal <?php echo $VERSION ?>  </h1>   
			<a href="#" name="features" class="dialog">[Features]</a> &middot; <a href="#" name="changelog" class="dialog">[Changelog]</a> &middot; <a href="#" name="help" class="dialog">[Hilfe]</a>
		</div>
		<hr style="margin:0px -10px;"><br>
		<noscript><span class='error'>Bitte aktiviere Javascript, damit WebOpal ordentlich funktioniert. Wir brauchen das f&uuml;r das Akkordion, sowie f&uuml;r die Ajax-Requests zur Auswertung des Opalcodes.</span><br></noscript>
		<a href="#" id="restore_exampl">Hello World!</a>
		<div id="warning" style="display:none;"><br><br><h1 style="display:inline;">Bitte aktiviere Cookies!</h1><span>(was sind <a href="http://de.wikipedia.org/wiki/HTTP-Cookie" target="_blank">Cookies</a>?)</span></div><br><br>
		<form enctype="multipart/form-data" action="index.php" method="POST" id="mainsubmit">
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
				<input type="button" value="Struktur hinzuf&uuml;gen" id="addStruc" <?php if($_SESSION['structnr']==$MAXFILES) {echo "disabled";} ;?>>
				<input type="text" id="structnr" name="structnr" value="<?php echo $_SESSION['structnr'];?>">
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
	<?php include "piwik.php"; ?>
</body>

</html>

<?php
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
