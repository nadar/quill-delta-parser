# Quill Delta Parser Upgrade

This document will help you upgrading from a version into another. For more detailed informations about the breaking changes **click the issue detail link**, there you can see examples of how to change your code.

## from 3.3.x to 3.4

+ [#80](https://github.com/nadar/quill-delta-parser/issues/80) In this update, we have introduced a change where background color information from Quill JSON is now extracted and applied as the background color. This change may result in unexpected behavior if you had background color information present in your documents but it was not rendered correctly in previous versions. To restore the previous behavior and resolve any unexpected issues related to background color rendering, please follow these steps:

```php
$backgroundColor = new BackgroundColor();
$backgroundColor->ignore = true;

// override the default listener behavior for background color:
$lexer = new Lexer($json);
$lexer->registerListener($backgroundColor);
echo $lexer->render();
```

## from 2.x to 3.0

+ [#61](https://github.com/nadar/quill-delta-parser/pull/61) PHP 7.1 is official dropped in unit tests. The code should still work with PHP 7.1 until next major release.
+ [#60](https://github.com/nadar/quill-delta-parser/pull/60) Changed the behavior of `nadar\quill\listener\Link`. Property `$wrapper` has been removed and replaced by `$wrapperOpen`, `$wrapperMiddle` and `$wrapperClose`. If you are not overriding or have a customized setup the `Link` listenere, upgrading is safe.

## from 1.x to 2.0

+ [#18](https://github.com/nadar/quill-delta-parser/issues/18) The input values from are now safely encoded by default. This might be a problem when using script or other raw html code inside your text(quill delta). To achieve the same output as behavior as before set `$lexer->escapeInput = false`. This will turn off input encoding but is not recommend.
