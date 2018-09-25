# Quill Delta to HTML Parser

A PHP library to parse [Quill WYSIWYG](https://quilljs.com/) [Deltas](https://github.com/quilljs/delta) into HTML - Flexibel and extendible for custom elements. Every element is parsed by the same mechanism, this makes it easy to extend and understand.

[![Build Status](https://travis-ci.org/nadar/quill-delta-parser.svg?branch=master)](https://travis-ci.org/nadar/quill-delta-parser)
[![Maintainability](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/maintainability)](https://codeclimate.com/github/nadar/quill-delta-parser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/test_coverage)](https://codeclimate.com/github/nadar/quill-delta-parser/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/nadar/quill-delta-parser/v/stable)](https://packagist.org/packages/nadar/quill-delta-parser)
[![Total Downloads](https://poser.pugx.org/nadar/quill-delta-parser/downloads)](https://packagist.org/packages/nadar/quill-delta-parser)
[![License](https://poser.pugx.org/nadar/quill-delta-parser/license)](https://packagist.org/packages/nadar/quill-delta-parser)

What is Quill? Quill is a free, open source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.

## Installation

The package is only available trough composer:

```sh
composer require nadar/quill-delta-parser
```

## Usage

```php
use nadar\quill\Lexer;

$lexer = new Lexer($json);

// echoing the html for the given json ops.
echo $lexer->render();
```

Where `$json` is the ops json array from quill, for example:

```json
{
  "ops": [
    {
      "insert": "Hello"
    },
    {
      "attributes": {
        "header": 1
      },
      "insert": "\n"
    },
    {
      "insert": "\nThis is the php quill "
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "parser"
    },
    {
      "insert": "!\n"
    }
  ]
}
```

This would render the following HTML:

```html
<h1>Hello</h1>
<p>THis is the php quill <strong>parsers</strong>!</p>
```

## Credits

+ [Dean Blackborough](https://github.com/deanblackborough)