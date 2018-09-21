# Quill Delta to HTML Parser

A PHP library to parse quill delta into html. All attributes and elements are based on a single listenere machnism. Therefore this makes this library highligh flexible. Adding your own listener to match a Quill plugin is easy to handle.

[![Latest Stable Version](https://poser.pugx.org/nadar/quill-delta-parser/v/stable)](https://packagist.org/packages/nadar/quill-delta-parser)
[![Total Downloads](https://poser.pugx.org/nadar/quill-delta-parser/downloads)](https://packagist.org/packages/nadar/quill-delta-parser)
[![License](https://poser.pugx.org/nadar/quill-delta-parser/license)](https://packagist.org/packages/nadar/quill-delta-parser)

## Installation

The package is only distributed trough the composer packagist

```sh
composer require nadar/quill-delta-parser
```

## Usage:

```php
use nadar\quill\Lexer;

$lexer = new Lexer($json);
$lexer->initBuiltInListeners();

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