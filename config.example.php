<?php
$HOSTURL=''; //Set the host url here, e.g. http://opal.test.me/
$IMPRESSUM=''; //Set the url to your impressum, if necessary.
$MINFILES=2; //Minimum / standard number of structures
$MAXFILES=3; //Number of the Maximum Structures allowed
$TIMEOUT=10; //Number of seconds which the program is allowed to run
$TIMEOUTTXT='Your program maybe timed out (>'.$TIMEOUT.'s)'; //text shown, if program did timeout
$SESSIONTIMEOUT=600; //time in seconds until the session expire
$EXAMPLECODE_IMPL = 'DEF hello == "Hello World!"';
$EXAMPLECODE_SIGN = 'FUN hello : denotation';
$EXAMPLECODE_CMD = 'hello';
$ADVERTCOMMENT = '-- created with WebOpal '.$VERSION.' (https://github.com/gehaxelt/PHP-Webopal) on Server '.$HOSTURL; //comment, which appears in files
$TMPDIR = 'tmp'; //temp-directory
$RUNMAX = 10;
$DEBUGMODE = false; // Enable debug mode? Will show var_dump of $_SESSION in debug.php

// For Bugreport PHP >= 5.3.2 with cURL extension needed
$BUGREPORT = false;	//Enable, if you want the users to be able to report Bugs/Ideas.

//Please fill out the following in order to get the bugreport to work
$ISSUEREPO='';			//Repository where the Bugs should be reported, e.g. PHP-Webopal
$ISSUEUSER= '';		//User which owns the repository, e.g. gehaxelt
$GITHUBUSER='';		//User that makes the reports
$GITHUBPW='';			//Password of that user
$PUBLICKEY = '';		//Public Key of reCaptcha (can be obtained here http://recaptcha.net)
$PRIVATEKEY = '';		//Private Key of reCaptcha (can be obtained here http://recaptcha.net)
// Starting here not required for Bugreport

$FORBIDDENEMAIL =  //For Blocking trashmail emails
Array("0clickemail.com","noclickemail.com","10minutemail.com ","bofthew.com","jnxjn.com","klzlk.com","nepwk.com","nwldx.com","owlpic.com","pjjkp.com",
"prtnx.com","rmqkr.net","rppkn.com","rtrtr.com","tyldd.com","uggsrock.com","12houremail.com","12minutemail.com","1pad.de","akapost.com",
"anon-mail.de","anonbox.net","anonmails.de","anonymbox.com","antispam.de","antispam24.de","antispammail.de","b2cmail.de","breakthru.com",
"bspamfree.org","bugmenot.com","bumpymail.com","cam4you.cc","centermail.com","centermail.net","deadaddress.com","despammed.com",
"dispostable.com","dodgeit.com","dodgit.com","dontsendmespam.de","dotman.de","dudmail.com","dump-email.info","dumpmail.de","e4ward.com",
"edv.to","eintagsmail.de","emailgo.de","emailias.com","emailsensei.com","emailtemporanea.com","emailtemporanea.net","eyepaste.com",
"fakemail.fr","filzmail.com","frapmail.com","garbagemail.org","garliclife.com","getmails.eu","getonemail.com","gishpuppy.com","nurfuerspam.de",
"guerillamail.org","guerrillamail.biz","guerrillamail.com","guerrillamail.de","guerrillamail.info","guerrillamail.org","sharklasers.com",
"guerrillamailblock.com","haltospam.com","hidemail.de","bootybay.de","ieh-mail.de","plexolan.de","incognitomail.net","incognitomail.org ",
"instant-mail.de","sinnlos-mail.de","wegwerf-email-adressen.de","wegwerf-emails.de","ip6.li","irish2me.com","jetable.com","jetable.net",
"jetable.org","junk.to","kasmail.com","keepmymail.com","lhsdv.com","lifebyfood.com","lr78.com","luckymail.org","card.zp.ua","express.net.ua",
"infocom.zp.ua","mail.zp.ua","mycard.net.ua","delikkt.de","m21.cc","mail21.cc","mysamp.de","mail4trash.com","mailcatch.com","mailbiz.biz",
"mailde.de","mailde.info","mailms.com","mailorg.org","mailtv.net","mailtv.tv","ministry-of-silly-walks.de","maileater.com","maileimer.de",
"mailexpire.com","mailforspam.com","binkmail.com","bobmail.info","chogmail.com","devnullmail.com","mailin8r.com","mailinater.com","mailinator.com",
"mailinator.net","mailinator2.com","safetymail.info","slopsbox.com","sogetthis.com","spamherelots.com","SpamHerePlease.com","suremail.info",
"thisisnotmyrealemail.com","tradermail.info","veryrealemail.com","zippymail.info","mailita.tk","mailme24.com","mailnull.com","mailshell.com ","mailtome.de","mailtrash.net","makemetheking.com","mbx.cc","meltmail.com","messagebeamer.de","mintemail.com","mytempmail.com","mailmetrash.com",
"mt2009.com","mytrashmail.com","thankyou2010.com","trash2009.com","trashymail.com ","nervmich.net","nervtmich.net","wegwerfadresse.de","netzidiot.de",
"no-spam.ws","nospam4.us","nospamfor.us","nospammail.net","nowmymail.com","obobbo.com","ohaaa.de","blackmarket.to","omail.pro","thc.st","vpn.st",
"oneoffemail.com","oneoffmail.com","onlatedotcom.info","otherinbox.com","pookmail.com","privacy.net","privatdemail.net","fansworldwide.de","privy-mail.de","privymail.de",
"trashmailer.com","put2.net","quickinbox.com","realtyalerts.ca","mailseal.de","receiveee.com","safetypost.de","schafmail.de","schmeissweg.tk",
"schrott-email.de","secretemail.de","lolfreak.net","secure-mail.biz","secure-mail.cc","z1p.biz","SendSpamHere.com","senseless-entertainment.com",
"is.af","server.ms","us.af","shieldemail.com","sneakemail.com","sofort-mail.de","sofortmail.de","soodonims.com","spam.la","spam.su","spamail.de",
"spamavert.com","spambob.com","0815.ru","3d-painting.com","agedmail.com","ano-mail.net","bio-muesli.info","bio-muesli.net","brennendesreich.de","buffemail.com",
"bund.us","cust.in","dbunker.com","discardmail.com","discardmail.de","duskmail.com","emaillime.com","ero-tube.org","film-blog.biz","flyspam.com","fr33mail.info",
"geschent.biz","great-host.in","hochsitze.com","hulapla.de","imails.info","kulturbetrieb.info","m4ilweb.info","misterpinball.de","mypartyclip.de","nomail2me.com","nospamthanks.info",
"politikerclub.de","recode.me","s0ny.net","sandelf.de","spambog.com","spambog.de","spambog.ru","superstachel.de","teewars.org","thanksnospam.info",
"watch-harry-potter.com","watchfull.net","webm4il.info","spambox.us","spamcero.com","spamcorptastic.com","spamex.com ","spamfree.eu","spamfree24.com",
"spamfree24.de","spamfree24.info","spamfree24.org","antichef.net","spamgourmet.com","spamhole.com","spaminator.de","spaml.de","spammotel.com",
"spamobox.com","spamspot.com","SpamThisPlease.com","spamtrail.com","cheatmail.de","fivemail.de","giantmail.de","nevermail.de","spoofmail.de",
"stuffmail.de","trialmail.de","squizzy.de","stinkefinger.net","super-auswahl.de","teleworm.com","temp-mail.org","llogin.ru","odnorazovoe.ru",
"temp-mail.ru","tempail.com","tempemail.co.za","tempemail.net","beefmilk.com","dingbone.com","fudgerub.com","lookugly.com",
"smellfear.com","tempinbox.com","tempmailer.com","tempmailer.de","tempomail.fr","temporarily.de","temporaryemail.net","temporaryinbox.com",
"temporarymailaddress.com","thismail.net","topranklist.de","trash-mail.com","trash-mail.de","trashdevil.com","trashdevil.de","trashemail.de",
"trashmail.com","kurzepost.de","objectmail.com","proxymail.eu","punkass.com","rcpt.at","trash-mail.at","trashmail.at","trashmail.me",
"trashmail.net","wegwerfmail.de","wegwerfmail.net","wegwerfmail.org","trashmail.ws","twinmail.de","163.com","2prong.com","8127ep.com","antireg.ru",
"asdasd.ru","bugmenever.com","despam.it","disposeamail.com","dontreg.com","einmalmail.de","fakedemail.com ","hmamail.com","humaility.com","ignoremail.com",
"lawlita.com","losemymail.com","mailscrap.com","nabuma.com","nobugmail.com","nobuma.com",
"spamday.com","spamkill.info","spaml.com","tilien.com","trashinbox.com","yxzx.net","wasteland.rfc822.org","weg-werf-email.de","wegwerf-email.net","wegwerfemail.com",
"wegwerfemail.de","wh4f.org","willhackforfood.biz","whyspam.me","cool.fr.nf","courriel.fr.nf","jetable.fr.nf","mega.zik.dj","moncourrier.fr.nf",
"monemail.fr.nf","monmail.fr.nf","nomail.xl.cx","nospam.ze.tc","speed.1s.fr","yopmail.com","yopmail.fr","yopmail.net","youmailr.com","zehnminuten.de","zehnminutenmail.de");

//DONT CHANGE BELOW HERE
if($ISSUEREPO==''||$ISSUEUSER== ''||$GITHUBUSER==''||$GITHUBPW==''||$PUBLICKEY == ''||$PRIVATEKEY == ''){
	$BUGREPORT = false; 
}
if (!in_array('curl', get_loaded_extensions())) {
	$BUGREPORT = false;
}
?>
