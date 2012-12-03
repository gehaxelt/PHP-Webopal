editAreaLoader.load_syntax["opal"] = {
	'DISPLAY_NAME' : 'Opal'
	,'COMMENT_SINGLE' : {1 : '--'}
	,'COMMENT_MULTI' : {'/*' : '*/'}
	,'QUOTEMARKS' : {1: "'", 2: '"'}
	,'KEYWORD_CASE_SENSITIVE' : true
	,'KEYWORDS' : {
		'constants' : [
			'0', 'newline'
		]
		,'types' : [
			'nat', 'bool', 'denotation', 'nat', 'char',
			'real', 'seq', 'void', 'com'
		]
		,'statements' : [
			'IF', 'THEN', 'ELSE', 'FI', 'DEF', 'FUN', 'DATA',
			'TYPE', 'SORT','IMPORT','COMPLETELY','ONLY','IMPLEMENTATION',
			'SIGNATURE','ASSERT','ASSUME','THEORY','LAW','PROOF','OTHERWISE',
			'LET','WHERE','IN','ANDIF','ORIF','AS'
		]
 		,'keywords' : [
			'Nat', 'nat', 'Denotation', 'denotation', 'real',
			'Real','char','Char','RealConv','CharConv','NatConv','Seq','SeqConv','seq','BoolConv','bool','Bool'
		]
	}
	,'OPERATORS' :[
		'==','->','+', '-', '/', '*', '=', '<', '>', '%', '!', '?', ':', '&', '|='
	]
	,'DELIMITERS' :[
		'(', ')', '[', ']', '{', '}'
	]
	,'REGEXPS' : {
		'precompiler' : {
			'search' : '()(#[^\r\n]*)()'
			,'class' : 'precompiler'
			,'modifiers' : 'g'
			,'execute' : 'before'
		}
	}
	,'STYLES' : {
		'COMMENTS': 'color: #AAAAAA;'
		,'QUOTESMARKS': 'color: #6381F8;'
		,'KEYWORDS' : {
			'constants' : 'color: #EE0000;'
			,'types' : 'color: #0000EE;'
			,'statements' : 'color: #60CA00;'
			,'keywords' : 'color: #48BDDF;'
		}
		,'OPERATORS' : 'color: #FF00FF;'
		,'DELIMITERS' : 'color: #0038E1;'
		,'REGEXPS' : {
			'precompiler' : 'color: #009900;'
			,'precompilerstring' : 'color: #994400;'
		}
	}
};
