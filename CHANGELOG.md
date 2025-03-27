# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## 3.4.3 (27. March 2025)
+ [#96](https://github.com/nadar/quill-delta-parser/issues/96) Addded HTML rendering for lists with type "checked" and "unchecked".

## 3.4.2 (5. April 2024)

+ [#87](https://github.com/nadar/quill-delta-parser/issues/87) Fixed a bug where a line break preceding a list containing inline attributes results in improper HTML formatting for next paragraphs.

## 3.4.1 (13. March 2024)

+ [#84](https://github.com/nadar/quill-delta-parser/issues/84) Allow align `left` as possible value.

## 3.4.0 (14. September 2023)

> Please be aware that this release may impact the way Quill data is displayed in your frontend. Checkout the [upgrade document](UPGRADE.md) for more details.

+ [#80](https://github.com/nadar/quill-delta-parser/issues/80) Add missing background color listener (In this update, we have introduced a change where background color information from Quill JSON is now extracted and applied as the background color. This change may result in unexpected behavior if you had background color information present in your documents but it was not rendered correctly in previous versions. see [upgrade document](UPGRADE.md)

## 3.3.1 (24. March 2023)

+ [#78](https://github.com/nadar/quill-delta-parser/issues/78) Fixed a bug where lists with empty contents would break all output.

## 3.3.0 (10. March 2023)

+ [#77](https://github.com/nadar/quill-delta-parser/pull/77) Allow method chaining for `registerListener()` and `overwriteListener()`.
+ [#76](https://github.com/nadar/quill-delta-parser/pull/76) Addded php 8.2 for unit tests

## 3.2.1 (1. November 2022)

+ [#74](https://github.com/nadar/quill-delta-parser/issues/74) Fixed missing support for nested lists
+ [#73](https://github.com/nadar/quill-delta-parser/pull/73) More automation and testing with rector and auto commit on PR's for csfixer and rector.

## 3.2.0 (7. August 2022)

+ [#71](https://github.com/nadar/quill-delta-parser/pull/71) Added image width and height attributes to the image tag if available.
+ [#72](https://github.com/nadar/quill-delta-parser/pull/72) Raised from phpstan level 5 to 6

## 3.1.0 (24. July 2022)

+ [#68](https://github.com/nadar/quill-delta-parser/pull/68) Deprecated the magical getter `$pick->$name` in `nadar\quill\Pick` class, use `$pick->optionValue($name)` instead.
+ [#64](https://github.com/nadar/quill-delta-parser/pull/64) Replaced deprecated public $input property in `Line` with getter and setter methods `getLine()` and `setLine($input)`.
+ [#63](https://github.com/nadar/quill-delta-parser/pull/63/) Removed deprecated methods `loadBuiltinListeneres()` and `renderListeneres()`.
+ [#62](https://github.com/nadar/quill-delta-parser/pull/62/) Prettify the html output a little by adding newlines after every block level element. If you have custom listeners which call `BlockListener->wrapElement()` this will be added for your custom listeners as well, otherwise you'll need to add the newlines yourself.

## 3.0.0 (2. June 2022)

> This release contains breaks which might affect your application. Checkout the [upgrade document](UPGRADE.md) for more details.

+ [#60](https://github.com/nadar/quill-delta-parser/pull/60) Changed the behavior of `nadar\quill\listener\Link`. Property `$wrapper` has been removed and replaced by `$wrapperOpen`, `$wrapperMiddle` and `$wrapperClose`.
+ [#61](https://github.com/nadar/quill-delta-parser/pull/61) Official dropped PHP 7.1 support added PHP 8.1 instead.  

## 2.9.0 (5. April 2022)

+ [#58](https://github.com/nadar/quill-delta-parser/pull/58/files) Renamed misspelled method `loadBuiltinListeneres()` to `loadBuiltinListeners()` and `renderListeneres()` to `renderListeners()`. The old methods are still available for backwards compatibility, but deprecated and will be removed in 3.0.

## 2.8.0 (8. March 2022)

+ [#56](https://github.com/nadar/quill-delta-parser/pull/56) Provide new method to override existing listeners with `overwriteListener()`.

## 2.7.2 (27. October 2021)

+ [#53](https://github.com/nadar/quill-delta-parser/pull/53) Lists listener, check for type being an array for compatibility with [Vanilla Forums](https://vanillaforums.com/).

## 2.7.1 (5. October 2021)

+ [#51](https://github.com/nadar/quill-delta-parser/issues/51) Fixed issue where images wrapped in italics renders as text instead of showing the image.

## 2.7.0 (7. June 2021)

+ [#49](https://github.com/nadar/quill-delta-parser/issues/49) Added Code Block Listener, generates `<pre><code>...</code></pre>` enclosed output.

## 2.6.1 (1. June 2021)

+ [#48](https://github.com/nadar/quill-delta-parser/pull/48) PHP 8 compatibility.

## 2.6.0 (2. December 2020)

+ [#42](https://github.com/nadar/quill-delta-parser/pull/42) Added PHP 8 Support.
+ [#43](https://github.com/nadar/quill-delta-parser/pull/42) Moved CI from Travis to GitHub Actions. Added PHP 8 version in Test Scenario.

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
