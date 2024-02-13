# Quill Delta to HTML Parser

A PHP library to parse [Quill WYSIWYG](https://quilljs.com/) editor [deltas](https://github.com/quilljs/delta) into HTML - flexible and extendable for custom elements. Every element is parsed by the same mechanism, making it easy to extend and understand. It also sanitizes the output value, making it more secure, especially when using user-generated text.

![Tests](https://github.com/nadar/quill-delta-parser/workflows/Tests/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/maintainability)](https://codeclimate.com/github/nadar/quill-delta-parser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/fdf80e7b61e4505bc421/test_coverage)](https://codeclimate.com/github/nadar/quill-delta-parser/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/nadar/quill-delta-parser/v/stable)](https://packagist.org/packages/nadar/quill-delta-parser)
[![Total Downloads](https://poser.pugx.org/nadar/quill-delta-parser/downloads)](https://packagist.org/packages/nadar/quill-delta-parser)
[![License](https://poser.pugx.org/nadar/quill-delta-parser/license)](https://packagist.org/packages/nadar/quill-delta-parser)

What is Quill? Quill is a free, open source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.

## Installation

The package is available only through Composer:

```sh
composer require nadar/quill-delta-parser
```

## Usage

```php
// Ensure to load the autoload file from Composer somewhere in your application.
require __DIR__ . '/vendor/autoload.php';

// Create the lexer object with your given Quill JSON delta code (either PHP array or JSON string).
$lexer = new \nadar\quill\Lexer($json);

// Echo the HTML for the given JSON ops.
echo $lexer->render();
```

Where `$json` is the ops JSON array from Quill, for example:

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
      "insert": "\nThis is the PHP Quill "
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
<p>This is the PHP Quill <strong>parser</strong>!</p>
```

## Extend the Parser

To extend the Parser by adding your own listeners (this can be the case if you are using Quill plugins which generate custom delta code), you have to decide whether it's an:

+ Inline element: Replaces content with new parsed content, mostly the case when working with Quill extensions.
+ Block element: Encloses the whole input with a tag, for example, a heading.

An example for a mention plugin that generates the following delta `{"insert":{"mention":{"id":"1","value":"Basil","denotationChar":"@"}}}`; an inline plugin could look like this:

```php
class Mention extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        // Check if input is JSON, decodes to an array, and checks if the key "mention" 
        // exists. If yes, return the value for this key.
        $mention = $line->insertJsonKey('mention');
        if ($mention) {
            // Apply the inline behavior, updates the content and append to the next "block" element.
            // The value in this example would be "<strong>Basil</strong>".
            $this->updateInput($line, '<strong>'.$mention['value'].'</strong>');
        }
    }
}
```

Now register the listener:

```php
$lexer = new Lexer($json);
$lexer->registerListener(new Mention());
echo $lexer->render();
```

## Override Built-in Listeners

Certain listeners (image, video, color) produce an HTML output which may not suit your use case, so you have the option to override the properties of those plugins and re-register the Listener. Here's an example with the image tag:

```php
$image = new Image();


$image->wrapper = '<img src="{src}" class="my-image" />';

// Override the default listener behavior for image color:
$lexer = new Lexer($json);
$lexer->registerListener($image);
echo $lexer->render();
```

If you want to replace a class with your own image class, use `overwriteListener` to achieve the same result, but with your totally custom class. The reason is that listeners are registered by their class names.

```php
$mySuperClass = new class() extends Image {
  // Here is the custom class code ...
};

$lexer->overwriteListener(new Image(), $mySuperClass);
```

Or, of course, when you have a separate file for your class:

```php
class MySuperDuperImageClass extends Image
{
    // Here is the custom class code ...
}

$lexer->overwriteListener(new Image(), new MySuperDuperImageClass());
```

## Debugging

Sometimes, understanding how delta is handled and parsed can be challenging to debug. Therefore, you can use the debugger class, which will print a table with information about how the data is parsed.

```php
$lexer = new Lexer($json);
$lexer->render(); // Make sure to run the render before calling debugPrint().
 
$debug = new Debug($lexer);
echo $debug->debugPrint();
```

There is also a built-in Docker Compose file which provides access to the `output.php` file. The `output.php` helps to directly write content with the Quill editor while displaying what is rendered, including all debug information. To run this Docker webserver, execute the following command in the root directory of your Git repository clone:

```sh
docker-compose up
```

and visit `http://localhost:5555/` in your browser.

#### Credits

+ [Lode Claassen](https://github.com/lode)
+ [Dean Blackborough](https://github.com/deanblackborough)
