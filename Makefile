#Makefile for optimizing JavaScript

LOG_LEVEL = QUIET

JS = js/script.js

CSS = css/style.scss
CSSOUT = css/style.css

TOOLS = ./tools/closure-compiler/compiler.jar

SASSC = sass

# ADVANCED_OPTIMIZATIONS or SIMPLE_OPTIMIZATIONS
JSOPTIMIZE = ADVANCED_OPTIMIZATIONS


JSOUTPUT = js/script.min.js

EXTERNSURL = http://code.jquery.com/ui/1.9.2/jquery-ui.min.js http://code.jquery.com/jquery-1.8.3.min.js

EXTERNS = ./js/jquery-ui.min.js ./js/jquery-1.8.3.min.js
EXTERNS += js/ace.js js/ext-static_highlight.js js/ext-textarea.js js/keybinding-emacs.js js/keybinding-vim.js js/theme-chrome.js

all: help

$(JSOUTPUT): $(JS) $(EXTERNS)
		java -jar $(TOOLS) --compilation_level $(JSOPTIMIZE)  $(foreach var,$(EXTERNS),--externs $(var)) --js $(JS) --js_output_file $(JSOUTPUT) --warning_level $(LOG_LEVEL)

$(CSSOUT): $(CSS)
		$(SASSC) $(CSS) > $(CSSOUT)

compile: $(CSS) $(JSOUTPUT)

$(EXTERNS):
		cd js; wget $(EXTERNSURL) -N

help:
	echo "use \"make advanced-optimize\" for better JS compression\n and \"make standard-optimze\" for standard compression"

clean:
	rm $(OUTPUT)
