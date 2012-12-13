//timeout for session expiration
function checkIfTimeOut() {
	if(new Date().getTime()>sessionEnd){
     	$('#dialog').dialog();
     	$('#dialog').dialog( "destroy" );
		$('#dialog').html("Hallo Du,<br>Deine Session ist abgelaufen. Wir wollen nat&uuml;rlich nicht, dass du deine Daten verlierst.<br>Wenn du willst kannst du also die Session erneuern und weiterarbeiten und deine Daten herunterladen oder alles l&ouml;schen");
		$('#dialog').dialog({
		resizable:false,
			modal: true,
			draggable:false,
			title: "Session abgelaufen",
			width: 500,
			closeOnEscape: false,
		   open: function() { $(".ui-dialog-titlebar-close").hide(); },
			buttons: {
			       "Session erneuern": function() {
	       				$(".struccontainer").each(function(index){
								$(this).find(".impl_hidden").val(editors[$(this).find(".impl").attr("id")].getSession().getValue())
								$(this).find(".sign_hidden").val(editors[$(this).find(".sign").attr("id")].getSession().getValue())
							});
							$( "#dialog" ).dialog( "option", "title", "Warte" );
							$( "#dialog" ).dialog( "option", "buttons", [] );
							$('#dialog').html("Versuche Session zu erneuern");
							$.ajax({
								url : 'inc/ajax.php',
								type : 'GET',
								dataType: "json",
								data : $('#mainsubmit').serialize()+"&page=update",
								success : function() {
									sessionEnd = new Date().getTime()+sessionTimeOut;
									timeOutId = setInterval("checkIfTimeOut()",(sessionTimeOut/20));
			                 	$("#dialog").dialog( "destroy" );
								},
								error : function() {
									$('#dialog').html("Wir konnten leider deine Session nicht wiederherstellen!<br>Klicke auf okay, um eine neue Session zu starten");
									$( "#dialog" ).dialog( "option", "buttons", [ { text: "Ok", click: function() { window.location.href="index.php"; } }] );
								}
							});

	             },
	             "Alles löschen": function() {
							var answer = confirm ("Wirklich alles löschen?")
							if(answer){window.location.href="index.php";}
	             }
		   }
		});
		clearInterval(timeOutId);
		return true;
	}else{
		return false;
	}
}

function getIssueList(){
	$('#issueList').children('.content').html("L&auml;dt Issueliste von GitHub");
	$.ajax({
		url : 'inc/ajax.php',
		type : 'GET',
		dataType: "json",
		data : "page=issueList",
		success : function(data) {
			$('#issueList').children('.content').html(data);
			$('#issueList').children('.content').accordion({
			collapsible:true,
			active:false,
			heightStyle:"content"
			});
		},
		error : function(data) {
			$('#issueList').children('.content').html("Konnte Issueliste nicht Laden. <input type='button' onclick='getIssueList()' value='Erneut versuchen'>");
			$('#issueList').append("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
		}
	});
}

function getIssueForm(){
	$('#reportForm').children('.content').html("L&auml;dt Formular");
	$.ajax({
		url : 'inc/ajax.php',
		type : 'GET',
		dataType: "json",
		data : "page=issueForm",
		success : function(data) {
			$('#reportForm').children('.content').html(data);
			showRecaptcha("reCaptcha");
			validateIssueForm();
		},
		error : function(data) {
			$('#reportForm').children('.content').html("Konnte Formular nicht Laden");
			$('#reportForm').append("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
		}
	});
}

function validateIssueForm(){
	$("#reportData").validate({
	   debug: true,
		rules: {
			type: "required",
			title: {
				required: true,
				minlength: 5
			},
			description: {
				required: true,
				minlength: 20
			},
			agree: "required"
		},
		messages: {
			type: "Bitte Art des Issue auswählen",
			title: {
				required: "Bitte einen Titel eingeben",
				minlength: "Mindestens 5 Zeichen!"
			},
			description: {
				required: "Bitte eine möglichst genaue Beschreibung eingeben",
				minlength: "Die Beschreibung sollte schon länger als 20 Zeichen sein!"
			},
			agree: "<br>Schau Dir doch die Issues auf der linken Seite an"
		}
	});
	$('#issueSubmit').click(function(){
		if ($('#reportData').valid()){checkReCaptcha();}
	});
}

function checkReCaptcha(){
	challengeField = $("input#recaptcha_challenge_field").val();
	responseField = $("input#recaptcha_response_field").val();
	$('#issueSubmit').attr("disabled","disabled");
	$('#issueSubmit').val("Warte...");
	$.ajax({
		type: "POST",
		url: "inc/ajax.php?page=checkCaptcha",
		data: $('#reportData').serialize()+"&recaptcha_challenge_field=" + challengeField + "&recaptcha_response_field=" + responseField,
		dataType: "json",
		async: false,
		success : function(data) {
			if(data.success == true){
				$('#reportForm').children('.content').html("Dein Issue wurde erfolgreich submitted<br>");
				$('#reportForm').children('.content').append(data.succ);
				$('#issueList').children('.content').accordion("destroy");
				getIssueList();
				jQuery('#reportData').unbind('submit');
				$( "#dialog" ).dialog( "option", "buttons", [ { text: "Cool, danke!", click: function(){$( "#dialog" ).dialog("close");} }] );
			}else{
				$("#captchaStatus").html(data);
				$('#issueSubmit').removeAttr("disabled");
				$('#issueSubmit').val("Absenden");
				Recaptcha.reload();
			}
		},
		error : function(data) {
			$('#reportForm').children('.content').html("Leider konnte dein Issue nicht gesendet werden:");
			$('#reportForm').children('.content').append("HTTP-Status: "+data.status+" ("+data.statusText+")\n"+data.responseText);
		}
	});
}
