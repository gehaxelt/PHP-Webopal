var editors = new Array();
var sessionEnd = 0;
var timeOutId = 0;
var sessionTimeOut = 0;
var markers = new Array();
var markersEditor = new Array();
/* Execute if DOM is ready */
$(function() {
	/* Array for all the ACE editors */
	var currentStruc = 1;
	var maxStruc = 3;
	var strucPre = "c91c";
	var actTab = 0;
	var implEx = 'DEF hello == "Hello World!"';
	var signEx = 'FUN hello : denotation';
	var cmdEx = 'hello';
	var keySwitch=false;
	var gWasPressed = false;
	var clearKeyState = function() {
    gWasPressed = false;
}
	sessionTimeOut =  parseInt($('#timeOut').val());
	sessionEnd = new Date().getTime()+sessionTimeOut;
	timeOutId = setInterval("checkIfTimeOut()",(sessionTimeOut/20));
	
	/* initialize Accordion */
	$("#accordion").accordion({
		collapsible:false,
		heightStyle: "content",
		event: "mouseup",
		activate: function(event, ui){
			if(!keySwitch){
				s = ui.newPanel.find(".impl").attr("id");
				editors[s].focus();
			}
			keySwitch=false;
		}
	});
	$('#accordion').accordion( "option", "active", actTab);

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

	$(".ui-resizable").each(function(index) {
		$(this).resizable();
	});	

	$("#restore_exampl").click(function(){
		num=$('.num:first').val();
		editors["editor-impl-"+num].setValue(implEx);
		editors["editor-sign-"+num].setValue(signEx);
		$('#runFunction').val(cmdEx);
	});

	$(document).on("change",'.nameInput',function(event){
		num=$(this).parent().find('.num').val();
		name=$(this).val();
		checkSignAndImpl(num,name);
	});

	$(document).on("click",'.errorJump',function(event){
		event.preventDefault();
		try{
			var err = $.parseJSON($(this).attr("value"));
   	}catch(e){
			alert(e);
    	}
 		c=$('#accordion h3').index($(".nameInput[value="+err.file+"]").parent());
 		editor="editor-"+err.type+"-"+$(".nameInput[value="+err.file+"]").parent().find('.num').val();
 		keySwitch=true;
 		$('#accordion').accordion( "option", "active", c);
 		//alert(err.file);
 		editors[editor].focus();
		editors[editor].gotoLine(parseInt(err.toLine)+1,parseInt(err.toChar)+1,false);
	});

	$(document).on("click",'.delStruc',function(event){
		if($('.delStruc').size()>1){
			name=$(this).parent().find('.nameInput').val();
			var answer = confirm (name+" wirklich löschen?")
			if(answer){
				num=$(this).parent().find('.num').val();
				$('.nameInput[name="fileName['+num+']"]').parent().remove();
				$('.impl[id="editor-impl-'+num+'"]').parent().parent().remove();
				currentStruc--;
				impl = "editor-impl-"+num;
				sign = "editor-sign-"+num;
				delete(editors[impl]);
				delete(editors[sign]);
				if(currentStruc<maxStruc){$("#addStruc").removeAttr("disabled");}
				$('#structnr').val(currentStruc);
				if($('.delStruc').size()<=1){$('.delStruc').hide();}
				$('#accordion').accordion( "option", "active", num-1);
				$.ajax({
					url : 'inc/ajax.php',
					type : 'GET',
					dataType: "json",
					data : "page=update&structnr="+currentStruc+"&delete="+num,
					success : function() {sessionEnd = new Date().getTime()+sessionTimeOut;},
					error : function(data) {
						$('#dialog').html("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
						$('#dialog').dialog({title: "ERROR", width: 700});
					}
				});
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
			$('#structnr').val(currentStruc);
			if($('.delStruc').size()>1){$('.delStruc').show();}
			$.ajax({
				url : 'inc/ajax.php',
				type : 'GET',
				dataType: "json",
				data : "page=update&file="+strucNum+"&structnr="+currentStruc,
				success : function() {	sessionEnd = new Date().getTime()+sessionTimeOut;},
				error : function(data) {
					$('#dialog').html("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
					$('#dialog').dialog({title: "ERROR", width: 700});
				}
			});
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
		$.ajax({
			url : 'inc/ajax.php',
			type : 'GET',
			dataType: "json",
			data: $('#mainsubmit').serialize()+"&oasys=true&page=update",
			/* Populate output and activate button on success */
			success: function(data) {
				var date = new Date();
				var hh = date.getHours();if (hh < 10) {hh = "0"+hh;}
				var mm = date.getMinutes();if (mm < 10) {mm = "0"+mm;}
				var ss = date.getSeconds();if (ss < 10) {ss = "0"+ss;}
				$('#output').html("Letzte Ausf&uuml;hrung: "+ hh+":"+mm+":"+ss + "<br>" + data.log);
				$("#execute").attr("value","Programm ausführen");
				$("#execute").removeAttr("disabled");
				sessionEnd = new Date().getTime()+sessionTimeOut;
				errors = $.parseJSON(data.err);
				for(i=0;i<markers.length;i++){
					editor=markersEditor[i];
					marker=markers[i];
					editors[editor].getSession().removeMarker(marker);
				}
				markers = new Array();
				markersEditor = new Array();			
   		   for (var e in errors) {
					if (errors.hasOwnProperty(e)) {
						err = errors[e];
						editor="editor-"+err.type+"-"+$(".nameInput[value="+err.file+"]").parent().find('.num').val();
						var Range = require('ace/range').Range;
						r = new Range(parseInt(err.fromLine),parseInt(err.fromChar),parseInt(err.toLine),parseInt(err.toChar));
						m = editors[editor].getSession().addMarker(r, "warning", "text", true);
						markers.push(m);
						markersEditor.push(editor);
					}
				}
			},
			error : function(data) {
				$('#dialog').html("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
				$('#dialog').dialog({title: "ERROR", width: 700});
				$("#execute").attr("value","Programm ausf\u00FChren")
				$("#execute").removeAttr("disabled")
			}
		 });
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
		$.ajax({
			url : 'inc/ajax.php',
			type : 'GET',
			dataType: "json",
			data : "page="+name,
			success : function(data) {
				$('#dialog').html(data.text);
				$('#dialog').dialog({title: data.title, width: w});
				sessionEnd = new Date().getTime()+sessionTimeOut;
			},
			error : function(data) {
				$('#dialog').html("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
				$('#dialog').dialog({title: "ERROR", width: 700});
			}
		});
	});

	$('#runFunction').keypress(function(e){
		if (e.which == 13) {
			e.preventDefault();
			$("#execute").click();
		}
	});

	/* Bind action for ctrl+space code completion */
	$(document).keydown(function(e){
		if( (e.ctrlKey||e.metaKey) && (e.charCode || e.keyCode) == 13 ) {
			$('#execute').click();
		}
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
			var possibleWords = new Array("IMPORT","denotation","Denotation","COMPLETELY","ONLY","NatConv","RealConv","CharConv","WHERE", "newline", "SIGNATURE", "IMPLEMENTATION");

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
		
		/* For the future, if Alt-G is pressed activate "jumpModus */
		//}else if( (e.altKey||e.metaKey) && String.fromCharCode(e.charCode || e.keyCode)=="G"){
		//	e.preventDefault();
		//	gWasPressed = true;
		//	setTimeout(clearKeyState, 3000);
		/* Jump with Alt-Numpad[2,4,6,8] */
		
		}else if(
				//(gWasPressed && someAction)|| 
				((e.altKey||e.metaKey) && (e.ctrlKey) && (-1!=$.inArray((e.charCode || e.keyCode), [98,100,102,104])))
		){
			e.preventDefault();
			var editorPos = new Array();
			for(editor in editors){
				editorPos.push(editor);
			}
			
			var pos=$.inArray($('.ace_focus').attr("id"),editorPos);
			
			if(pos!=-1){
				switch (e.charCode || e.keyCode) {
					case 104:
						if(pos-2<0){pos=editorPos.length-2+pos%2;}else{pos=pos-2;}
						break;
					case 100:
						if(pos-1<0){pos=editorPos.length-1;}else{pos=pos-1;}
						break;
					case 98:
						if(pos+2>editorPos.length-1){pos=pos%2;}else{pos=pos+2;}
						break;
					case 102:
						if(pos+1>editorPos.length-1){pos=0;}else{pos=pos+1;}
						break;
				}
			
				keySwitch=true;
			
				$('#accordion').accordion( "option", "active", (pos-(pos%2))/2);
				editors[editorPos[pos]].focus();
			}
		}
	});

	/* Print warning if cookies are disabled */
	if (navigator.cookieEnabled != true) {
	  $('#warning').show()
	}

	if($('.delStruc').size()<=1){
		$('.delStruc').hide();
	}
	
    $('#bugReport').click(function(){
     	$('#dialog').html("<div id='issueList'><h3 class='title'>Issueliste</h3><div class='content'></div></div><div id='reportForm'><h3 class='title'>Reportformular</h3><div class='content'></div></div>");
     	$('#dialog').dialog().dialog("destroy");
    	$('#dialog').dialog({
    			title : "Bugreport / Idee einreichen",
    			width : "80%",
    			height : $(window).height()*0.8,
    			modal:true,
    			open: function(){
    				$('.ui-dialog').css("position","fixed");
    				clearInterval(timeOutId);
    			},
    			close: function(){
    				$('.ui-dialog').css("position","absolute");
    				if(!checkIfTimeOut()){
	    				timeOutId = setInterval("checkIfTimeOut()",(sessionTimeOut/20));
    				}
    			}
    	});
    	getIssueForm();
    	getIssueList();
    });
   
});

function objToString (obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}
