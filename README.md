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
<p>This is the php quill <strong>parser</strong>!</p>
```

## Extend

In order to extend the Parser by adding your own listeneres (this can be the case if you are using quill plugins which generates custom delta code), you have to decide whether its:

+ inline element: Replaces content with new parsed content, this is mostly the case when working with quill extensions.
+ block element: Block elements which encloses the whole input with a tag, for example heading.

An example for a mention plugin which generates the following delta `{"insert":{"mention":{"id":"1","value":"Basil","denotationChar":"@"}}}` an inline plugin could look like this:

```php
class Mention extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        // as "insert" value is not a string, it contains json notiation content:
        if ($line->isJsonInsert()) {
            // parse the given json into an array
            $array = $line->getArrayInsert();
            // it seems the array has a key with the name "mention":
            if (isset($array['mention'])) {
                // change the output of the current line with the value from the mention array config
                $line->output = $array['mention']['value'];
                // mark as inline, so other elements will not thread as block
                $line->setAsInline();
                // mark the line as done, so no other plugin will interact with the line
                $line->setDone();
            }
        }
    }
}
```

Now register the listenere:

```php
$lexer = new Lexer($json);
$lexer->registerListener(new Mention);
echo $lexer->render();

## Credits

+ [Dean Blackborough](https://github.com/deanblackborough)