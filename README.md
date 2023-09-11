# Quill Delta to HTML Parser

A PHP library to parse [Quill WYSIWYG](https://quilljs.com/) editor [deltas](https://github.com/quilljs/delta) into HTML - flexible and extendible for custom elements. Every element is parsed by the same mechanism, this makes it easy to extend and understand. It will also sanitize the output value, so its more secure especially when using user generated text.

![Tests](https://github.com/nadar/quill-delta-parser/workflows/Tests/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/maintainability)](https://codeclimate.com/github/nadar/quill-delta-parser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/test_coverage)](https://codeclimate.com/github/nadar/quill-delta-parser/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/nadar/quill-delta-parser/v/stable)](https://packagist.org/packages/nadar/quill-delta-parser)
[![Total Downloads](https://poser.pugx.org/nadar/quill-delta-parser/downloads)](https://packagist.org/packages/nadar/quill-delta-parser)
[![License](https://poser.pugx.org/nadar/quill-delta-parser/license)](https://packagist.org/packages/nadar/quill-delta-parser)

What is Quill? Quill is a free, open source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.

## Installation

The package is only available through composer:

```sh
composer require nadar/quill-delta-parser
```

## Usage

```php
// ensure to load the autoload file from composer somewhere in your application
require __DIR__ . '/vendor/autoload.php';

// create the lexer object with your given quill json delta code (either PHP array or JSON string)
$lexer = new \nadar\quill\Lexer($json);

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
<p><br></p>
<p>This is the php quill <strong>parser</strong>!</p>
```

## Extend the Parser

In order to extend the Parser by adding your own listeners (this can be the case if you are using quill plugins which generates custom delta code), you have to decide whether it's an:

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
        // check if input is json, decodes to an array and checks if the key "mention" 
        // exists, if yes return the value for this key.
        $mention = $line->insertJsonKey('mention');
        if ($mention) {
            // apply the inline behavior, updates the content and append to next "block" element.
            // the value in this example would be "<strong>Basil</strong>".
            $this->updateInput($line, '<strong>'.$mention['value'].'</strong>');
        }
    }
}
```

Now register the listener:

```php
$lexer = new Lexer($json);
$lexer->registerListener(new Mention);
echo $lexer->render();
```

## Override built-in Listeners

Certain listeners (image, video, color) produce a HTML output which maybe not suit your use case, so you have the option to override the properties of those plugins and re-register the Listener, here an example with the image tag:

```php
$image = new Image();
$image->wrapper = '<img src="{src}" class="my-image" />';

// override the default listener behavior for image color:
$lexer = new Lexer($json);
$lexer->registerListener($image);
echo $lexer->render();
```

If the class name has changed by our own image class, you can use `overwriteListener` to achieve the same result, but with your total custom class. The reason is that the listeners are registered by its class names.

```php
$mySuperClass = new class() extends Image {
  // here is the custom class code ...
}

$lexer->overwriteListener(new Image, $mySuperClass);
```

Or, of course when you have a seperate file for your class

```php
class MySuperDuperImageClass extends Image
{
    // here is the custom class code ...
}

$lexer->overwriteListener(new Image, new MySuperDuperImageClass);
```

## Debuging

Sometimes the handling of delta and how the data is parsed is very hard to debug and understand. Therefore you can use the debugger class which will print a table with informations about how the data is parsed.

```php
$lexer = new Lexer($json);
$lexer->render(); // make sure to run the render before call debugPrint();
 
$debug = new Debug($lexer);
echo $debug->debugPrint();
```

There is also a built in docker compose file which provides access to the output.php file. The output.php helps to directly write content with the quill editor while displaying what is rendered including all debug informations. In order to run this docker webserver execute the following command in the root directory of your git repository clone:

```sh
docker-compose up
```

and visit `http://localhost:5555/` in your browser.

#### Credits

+ [Lode Claassen](https://github.com/lode)
+ [Dean Blackborough](https://github.com/deanblackborough)
