#!/bin/bash

## Uncompresses the comiled style.fallback.css into readable code.
## Saves the result in style.fallback.readable.css
sed -e 's/;/;\n\t/g' ../css/style.fallback.css | sed -e 's/}/\n}\n\n/g' - | sed -e 's/{/{\n\t/g' - >../css/style.fallback.readable.css

