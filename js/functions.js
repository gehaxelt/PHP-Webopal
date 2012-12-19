/* FUNCTIONS.js */

//timeout for session expiration
function checkIfTimeOut() {
	if (new Date().getTime() > sessionEnd) {
		$('#dialog').dialog();
		$('#dialog').dialog("destroy");
		$('#dialog').html("Hallo Du,<br> Deine Session ist abgelaufen. Wir wollen nat&uuml;rlich nicht, dass du deine Daten verlierst. <br> Wenn du willst kannst du also die Session erneuern und weiterarbeiten und deine Daten herunterladen oder alles l&ouml;schen");
		$('#dialog').dialog({
			resizable: false,
			modal: true,
			draggable: false,
			title: "Session abgelaufen",
			width: 500,
			closeOnEscape: false,
			open: function () { $(".ui-dialog-titlebar-close").hide(); },
			buttons: {
				"Session erneuern": function () {
					if (navigator.onLine) {
						$(".struccontainer").each(function (index) {
							$(this).find(".impl_hidden").val(editors[$(this).find(".impl").attr("id")].getSession().getValue());
							$(this).find(".sign_hidden").val(editors[$(this).find(".sign").attr("id")].getSession().getValue());
						});
						$("#dialog").dialog("option", "title", "Warte");
						$("#dialog").dialog("option", "buttons", []);
						$('#dialog').html("Versuche Session zu erneuern");
						$.ajax({
							url: 'inc/ajax.php',
							type: 'GET',
							dataType: "json",
							data: $('#mainsubmit').serialize() + "&page=update",
							success: function () {
								sessionEnd = new Date().getTime() + sessionTimeOut;
								timeOutId = setInterval(checkIfTimeOut, (sessionTimeOut / 20));
								$("#dialog").dialog("destroy");
							},
							error: function () {
								$('#dialog').html("Wir konnten leider deine Session nicht wiederherstellen!<br>Klicke auf okay, um eine neue Session zu starten");
								$("#dialog").dialog("option", "buttons", [ { text: "Ok", click: function () { window.location.href = "index.php"; } }]);
							}
						});
					} else {
						alert("Keine Internetverbindung");
						//TODO: add cookie-based saving via JS
					}
	            },
				"Alles löschen": function () {
					var answer = confirm("Wirklich alles löschen?");
					if (answer) {window.location.href = "index.php"; }
				}
			}
		});
		clearInterval(timeOutId);
	}
}

function getIssueList() {
	$('#issueList').children('.content').html("L&auml;dt Issueliste von GitHub");
	$.ajax({
		url: 'inc/ajax.php',
		type: 'GET',
		dataType: "json",
		data: "page=issueList",
		success: function (data) {
			$('#issueList').children('.content').html(data);
			$('#issueList').children('.content').accordion({
				collapsible: true,
				active: false,
				heightStyle: "content"
			});
		},
		error: function (data) {
			$('#issueList').children('.content').html("Konnte Issueliste nicht Laden. <input type='button' onclick='getIssueList()' value='Erneut versuchen'>");
			$('#issueList').append("HTTP-Status: " + data.status + " (" + data.statusText + ")\n" + data.responseText);
		}
	});
}

function checkReCaptcha() {
	var challengeField = $("input#recaptcha_challenge_field").val(), responseField = $("input#recaptcha_response_field").val();
	$('#issueSubmit').attr("disabled", "disabled");
	$('#issueSubmit').val("Warte...");
	$.ajax({
		type: "POST",
		url: "inc/ajax.php?page=checkCaptcha",
		data: $('#reportData').serialize() + "&recaptcha_challenge_field=" + challengeField + "&recaptcha_response_field=" + responseField,
		dataType: "json",
		async: false,
		success: function (data) {
			if (data.success == true) {
				$('#reportForm').children('.content').html("Dein Issue wurde erfolgreich submitted<br>");
				$('#reportForm').children('.content').append(data.succ);
				$('#issueList').children('.content').accordion("destroy");
				getIssueList();
				$('#reportData').unbind('submit');
				$("#dialog").dialog("option", "buttons", [ { text: "Cool, danke!", click: function () {$("#dialog").dialog("close"); } }]);
			} else {
				$("#captchaStatus").html(data);
				$('#issueSubmit').removeAttr("disabled");
				$('#issueSubmit').val("Absenden");
				Recaptcha.reload();
			}
		},
		error: function (data) {
			$('#reportForm').children('.content').html("Leider konnte dein Issue nicht gesendet werden:");
			$('#reportForm').children('.content').append("HTTP-Status: " + data.status + " (" + data.statusText + ")\n" + data.responseText);
		}
	});
}

function validateIssueForm() {
	$("#reportData").validate({
		debug: true,
		onkeyup: false,
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
			agree: "required",
			email: {
				required: true,
				email: true,
				remote: {
					url: "inc/ajax.php",
					type: "get",
					data: {
						page: 'trashmail'
					}
				}
			}
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
			agree: "<br>Schau Dir doch die Issues auf der linken Seite an",
			email: {
				required: "Das E-Mailfeld wird benötigt",
				email: "Bitte eine E-Mailadresse im Format foo@foo.de angeben",
				remote: "Bitte keine Trashmailadresse!"
			}
		}
	});
	$('#issueSubmit').click(function () {
		if ($('#reportData').valid()) { checkReCaptcha(); }
	});
}

function getIssueForm() {
	$('#reportForm').children('.content').html("L&auml;dt Formular");
	$.ajax({
		url: 'inc/ajax.php',
		type: 'GET',
		dataType: "json",
		data: "page=issueForm",
		success: function (data) {
			$('#reportForm').children('.content').html(data);
			showRecaptcha("reCaptcha");
			$('.whyEmail').hide();
			$('#whyEmail').hover(
				function () {
					$('.whyEmail').show('fast');
				},
				function () {
					$('.whyEmail').hide('fast');
				}
			);
			validateIssueForm();
		},
		error: function (data) {
			$('#reportForm').children('.content').html("Konnte Formular nicht Laden");
			$('#reportForm').append("HTTP-Status: " + data.status + " (" + data.statusText + ")\n" + data.responseText);
		}
	});
}

function checkSignAndImpl(num, name) {
	editors["editor-impl-" + num].find(/IMPLEMENTATION\s*([A-Za-z0-9]*)/, {regExp: true});
	editors["editor-impl-" + num].replace('IMPLEMENTATION ' + name);
	editors["editor-sign-" + num].find(/SIGNATURE\s*([A-Za-z0-9]*)/, {regExp: true});
	editors["editor-sign-" + num].replace('SIGNATURE ' + name);
}

function resizeElements(event, ui) {
	if (new Date().getTime() - lastResize > 50) {
		var also = ui.element.children(".resizeAlso").attr("id"), nHa = ui.element.children(".resizeNot").height(), nH;
		$('#' + also).css({
			height: (ui.size.height - 25 - nHa) + "px",
			marginTop: (nHa + 7) + "px"
		});
		editors[also].resize();
		ui.element.siblings('.resizeEditor').css({
			height: ui.size.height + "px",
			width: (maxWidth - ui.size.width) + "px"
		});
		also = ui.element.siblings('.resizeEditor').children(".resizeAlso").attr("id");
		nH = ui.element.siblings('.resizeEditor').children(".resizeNot").height();
		$('#' + also).css({
			height: (ui.size.height - 25 - nH) + "px",
			marginTop: (nH + 7) + "px"
		});
		editors[also].resize();
		lastResize = new Date().getTime();
	}
}

function initResize() {
	$(".resizeEditor").not('.resizeInitialized').addClass('resizeInitialized').on().resizable({
		maxWidth: maxWidth * 0.8,
		minWidth: maxWidth * 0.2,
		resize: function (event, ui) {resizeElements(event, ui); },
		stop: function (event, ui) {lastResize = 0; resizeElements(event, ui); }
	});
}

function split(val) {
	return val.split(/;\s*/);
}
function extractLast(term) {
	return split(term).pop();
}

function objToString(obj) {
    var p, str = '';
    for (p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}

/* END OF functions.js */