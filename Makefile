#Makefile for optimizing JavaScript

LOG_LEVEL = QUIET

JS = script.js

TOOLS = ./tools/closure-compiler/compiler.jar

OUTPUT = script.min.js

EXTERNSURL = http://code.jquery.com/ui/1.9.1/jquery-ui.min.js http://code.jquery.com/jquery-1.8.3.min.js

EXTERNS = ./externs/jquery-ui.min.js ./externs/jquery-1.8.3.min.js
EXTERNS += ace/ace.js ace/ext-static_highlight.js ace/ext-textarea.js ace/keybinding-emacs.js ace/keybinding-vim.js ace/theme-chrome.js

all: help

advanced-optimize: $(JS) download-externs
		java -jar $(TOOLS) --compilation_level ADVANCED_OPTIMIZATIONS  $(foreach var,$(EXTERNS),--externs $(var)) --js $(JS) --js_output_file $(OUTPUT) --warning_level $(LOG_LEVEL)

standard-optimize: $(JS) download-externs
		java -jar $(TOOLS) --compilation_level SIMPLE_OPTIMIZATIONS $(foreach var,$(JS),--js $(var)) $(foreach var,$(EXTERNS),--externs $(var)) --js_output_file $(OUTPUT) --warning_level $(LOG_LEVEL)

download-externs:
		mkdir -p externs
		cd externs; wget $(EXTERNSURL) -N

help:
	echo "use \"make advanced-optimize\" for better JS compression\n and \"make standard-optimze\" for standard compression"

clean:
	rm $(OUTPUT)
