#Makefile for optimizing JavaScript

LOG_LEVEL = QUIET

JS = js/script.js
JS2 = js/functions.js

CSS = css/style.scss css/ace.scss css/extern.scss
CSSOUT = css/style.css

TOOLS = ./tools/closure-compiler/compiler.jar

SASSC = sass
SASSFLAGS = --style compressed



# ADVANCED_OPTIMIZATIONS or SIMPLE_OPTIMIZATIONS
JSOPTIMIZE = SIMPLE_OPTIMIZATIONS

JSOUTPUT = js/script.min.js

EXTERNSURL = http://code.jquery.com/ui/1.9.2/jquery-ui.min.js http://code.jquery.com/jquery-1.8.3.min.js

JSEXTERNS = ./js/jquery-ui.min.js ./js/jquery-1.8.3.min.js
JSEXTERNS += js/ace.js js/ext-static_highlight.js js/ext-textarea.js js/keybinding-emacs.js js/keybinding-vim.js js/theme-chrome.js

all: help

$(JSOUTPUT): $(JS) $(JS2) $(JSEXTERNS)
		java -jar $(TOOLS) --compilation_level $(JSOPTIMIZE)  $(foreach var,$(JSEXTERNS),--externs $(var)) --js $(JS2) --js $(JS) --js_output_file $(JSOUTPUT) --warning_level $(LOG_LEVEL)

$(CSSOUT): $(CSS)
		$(SASSC) $(SASSFLAGS) $< > $@

compile: check $(CSSOUT) $(JSOUTPUT)

compile-js: check $(JSOUTPUT)

compile-css: check $(CSSOUT)

$(JSEXTERNS):
		cd js; wget $(EXTERNSURL)


help:
	echo "use \"make compile\" for compiling JavaScript and CSS\n or \"make compile-js\" or \"make compile-css\" to compile seperately\n or you can specify which optimzation with \"make compile JSOPTIMIZE=ADVANCED_OPTIMIZATIONS\""

#test if dependencies installed
check: SASS-installed RUBY-installed

SASS-installed:
		@which sass > /dev/null || { echo "please install sass (http://sass-lang.com/)"; exit 1; }

RUBY-installed:
		@which ruby > /dev/null || { echo "please install ruby (http://www.ruby-lang.org/)"; exit 1; }
		@which gem > /dev/null || { echo "please install ruby (http://www.ruby-lang.org/)"; exit 1; }

clean:
	rm $(JSOUTPUT)
	rm $(CSSOUT)
