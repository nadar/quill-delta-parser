# Quill Delta Parser Upgrade

This document will help you upgrading from a version into another. For more detailed informations about the breaking changes **click the issue detail link**, there you can see examples of how to change your code.

## from 2.x to 3.0

## from 1.x to 2.0

+ [#18](https://github.com/nadar/quill-delta-parser/issues/18) The input values from are now safely encoded by default. This might be a problem when using script or other raw html code inside your text(quill delta). To achieve the same output as behavior as before set `$lexer->escapeInput = false`. This will turn off input encoding but is not recommend.