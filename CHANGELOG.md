# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## 2.6.0 (2. December 2020)

+ [#42](https://github.com/nadar/quill-delta-parser/pull/42) Added PHP 8 Support.
+ [#43](https://github.com/nadar/quill-delta-parser/pull/42) Moved CI from Travis to GitHub Actions. Added PHP 8 support in Test Scenario.

## 2.5.0 (29. June 2020)

+ [#41](https://github.com/nadar/quill-delta-parser/pull/41) Add option to configure Embed Video `allow` option.

## 2.4.0 (2. November 2019)

+ [#32](https://github.com/nadar/quill-delta-parser/pull/32) Added new `wrapElement` method to simplify building block listeners.
+ [#30](https://github.com/nadar/quill-delta-parser/issues/30) List opening tag process has been simplified in order to support single bullet lists.
+ [#31](https://github.com/nadar/quill-delta-parser/pull/31) Add support for align attribute.

## 2.3.0 (23. October 2019)

+ Add override functionality to links, allowing to customise their wrapper.

## 2.2.0 (17. October 2019)

+ Add inline element to handle script attribute.

## 2.1.1 (2. September 2019)

+ [#25](https://github.com/nadar/quill-delta-parser/issues/25) Ensure that empty heading listeners won't destroy all upcoming elements.

## 2.1.0 (24. August 2019)

+ [#23](https://github.com/nadar/quill-delta-parser/issues/23) Add inline element to handle font attribute.

## 2.0.0 (10. July 2019)

> This release contains breaks which might affect your application. Checkout the [upgrade document](UPGRADE.md) for more details.

+ [#18](https://github.com/nadar/quill-delta-parser/issues/18) Enable `escapeInput` option by default in order to increase security.

## 1.3.2 (9. July 2019)

+ [#13](https://github.com/nadar/quill-delta-parser/pull/13) Fixed bug when lists are interrupted with block level elements (e.g. videos)
+ [#16](https://github.com/nadar/quill-delta-parser/issues/16) Fixed bug with sorting index of inline elements when using prepend.
+ [#15](https://github.com/nadar/quill-delta-parser/pull/15) Fixed bug when header contains (partial) formatting and line before header contains formatting

## 1.3.1 (6. June 2019)

+ [#12](https://github.com/nadar/quill-delta-parser/issues/12) Fixed bug when using quill parser on windows platforms.

## 1.3.0 (28. May 2019)

+ Added new debugInfo option for lines
+ Added new behavior for hasNewline (which is now more consistent according to delta input)
+ [#8](https://github.com/nadar/quill-delta-parser/issues/8) Fixed issue with UL/OL list elements if only one element is inside the list.

## 1.2.0 (30. April 2019)

+ [#7](https://github.com/nadar/quill-delta-parser/pull/7) Makes sure input and attributes from delta is escaped before mixing it with html. Listeners should use `$line->getInput()` instead of `$line->input` to read input. This will properly escape if it is not done already. Values from attributes should be passed through `$line->getLexer()->escape()`. See the [Color listener](src/listener/Color.php) for an example of both. Obviously, escaping should be skipped in case a listener is meant to output raw html.

## 1.1.1 (17. April 2019)

+ [#6](https://github.com/nadar/quill-delta-parser/pull/6) Fixed bug in exception messaged. Added unit tests and improved message.

## 1.1.0 (8. March 2019)

+ [#5](https://github.com/nadar/quill-delta-parser/issues/5) Fixed a bug where paragraphs with attributes where not rendered (color attribute). Added new parameter to disable the rendering of attributes.

## 1.0.2 (7. February 2019)

+ Added Image-Tag for image output.

## 1.0.1 (25. November 2018)

+ Improve Video-Tag output (Remove frameborder, use youtube embed code allow tag).

## 1.0.0 (21. October 2018)

+ First stable API release.
