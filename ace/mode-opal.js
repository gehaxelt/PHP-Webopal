//Template

define('ace/mode/opal', function(require, exports, module) {
"use strict";

var oop = require("../lib/oop");
// defines the parent mode
var TextMode = require("./text").Mode;
var Tokenizer = require("../tokenizer").Tokenizer;
//var MatchingBraceOutdent = require("../matching_brace_outdent").MatchingBraceOutdent;

// defines the language specific highlighters and folding rules
var OpalHighlightRules = require("ace/mode/opal_highlight_rules").OpalHighlightRules;
//var MyNewFoldMode = require("./folding/mynew").MyNewFoldMode;

var Mode = function() {
    // set everything up
    this.$tokenizer = new Tokenizer(new OpalHighlightRules().getRules());
//    this.$outdent = new MatchingBraceOutdent();
    //this.foldingRules = new MyNewFoldMode();


};
oop.inherits(Mode, TextMode);

(function() {
    // Extra logic goes here--we won't be covering all of this

    /* These are all optional pieces of code!
    this.getNextLineIndent = function(state, line, tab) {
        var indent = this.$getIndent(line);
        return indent;
    };

    this.checkOutdent = function(state, line, input) {
        return this.$outdent.checkOutdent(line, input);
    };

    this.autoOutdent = function(state, doc, row) {
        this.$outdent.autoOutdent(doc, row);
    };

    this.createWorker = function(session) {
        var worker = new WorkerClient(["ace"], "ace/mode/mynew_worker", "NewWorker");
        worker.attachToDocument(session.getDocument());

        return worker;
    };
    */
}).call(Mode.prototype);

exports.Mode = Mode;
});

define('ace/mode/opal_highlight_rules', function(require, exports, module) {

var oop = require("ace/lib/oop");
var TextHighlightRules = require("ace/mode/text_highlight_rules").TextHighlightRules;

var OpalHighlightRules = function() {
    var keywordMapper = this.createKeywordMapper({
        "opalhandler": "IMPORT|ONLY|COMPLETELY",
	"opalkeyword":
		"ALL|AND|ANDIF|ANY|AS|ASSERT|AXM|DATA|DEF|DERIVE|DFD|DESCRIMINATORS|ELSE|EX|EXTERNAL|FI|FIX|FUN|IF|IMPLEMENTATION|IMPLIES|IN|INHERIT|INJECTIONS|INTERFACE|INTERNAL|LAW|LAZY|LEFTASSOC|LET|MODULE|NOT|NOR|OR|ORIF|OTHERWISE|POST|PRE|PRED|PRIORITY|PROPERTIES|REALIZES|REQUIRE|RIGHTASSOC|SELECTORS|SIGNATURE|SORT|SPC|SPEC|SPECIFICATION|STRUCTURE|THE|THEN|THEORY|THM|TYPE|UNIQ|WHERE",
	"opaltype":
		"aEntry|agent|align|anchor|ans|arg|arg1|arg2|array|arrowWhere|bag|bitmap|bool|bstree|byte|callback|canvasEditor|capStyle|channel|char|childstat|codom|codomFrom|codomTo|color|colorModel|com|composeOp|config|configCom|cursor|dArray|data|data1|data11|data2|data21|data3|data31|data4|data41|dataFrom|dataTo|defaultPrio|denotation|device|dist|distOut|dom|domFrom|domTo|drawing|dyn|emitter|env|event|eventInfo|file|filemode|filestat|filetype|first|first1|first2|first3|fission|fmt|font|from|from1|from2|funct|group|groupid|heap|iconfig|image|in|inData|index|inode|input|int|inter|interdom|interpreter|iseq|items|joinStyle|justifyHow|long|manager|managerRequest|map|mapEntry|mark|mid|modifier|nat|natMap|OBJECT|option|orient|out|outData|output|packOp|pair|parser|permission|point|positionRequest|process|procstat|quad|range|real|regulator|rel|relief|res|res1|res2|result|role|sap|script|scroller|scrollView|scrollWindow|searchOpt|second|seekMode|selector|semaphor|seq|seqEntry|set|setEntry|short|sigaction|sighandler|sigmask|signal|size|sizeRequest|some|sreal|state|stateId|stateRequest|string|subrel|tag|textEditor|time|to|tree|triple|union|user|userid|version|view|void|wconfig|wconfigCom|wday|widget|window|wrapStyle|",
	"opaldigit":
		"0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|64|100|128|256|512|1000|1024|10000|100000|1000000",
	"opalbool":
		"true|false"
    }, "identifier");

   var identifierRe = "[0-9a-zA-Z\\$_\u00a1-\uffff][0-9a-zA-Z\\d\\$_\u00a1-\uffff]*\\b";

   this.$rules = {
        "start" : [
            {
                token: "opalcomment", // String, Array, or Function: the CSS token to apply
                regex: "-- .+$" // String or RegExp: the regexp to match
            },
            {
                token : "opalcomment", // multi line comment
                merge : true,
                regex : /\/\*/,
                next : "opalcomment"
            },
            {
                token : keywordMapper,
                regex : identifierRe
            }
        ],
        "opalcomment" : [
            {
                token : "opalcomment", // closing comment
                regex : ".*?\\*\\/",
                merge : true,
                next : "start"
            }, {
                token : "opalcomment", // comment spanning whole line
                merge : true,
                regex : ".+"
            }
        ]
    };
    
}

oop.inherits(OpalHighlightRules, TextHighlightRules);

exports.OpalHighlightRules = OpalHighlightRules;
});
