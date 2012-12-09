#Makefile for optimizing JavaScript

LOG_LEVEL = QUIET

JS = script.js

TOOLS = ./tools/closure-compiler/compiler.jar

OUTPUT = script.min.js

EXTERNSURL = http://code.jquery.com/ui/1.9.2/jquery-ui.min.js http://code.jquery.com/jquery-1.8.3.min.js

EXTERNS = ./js/jquery-ui.min.js ./js/jquery-1.8.3.min.js
EXTERNS += js/ace.js js/ext-static_highlight.js js/ext-textarea.js js/keybinding-emacs.js js/keybinding-vim.js js/theme-chrome.js

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
