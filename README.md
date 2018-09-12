# quill-delta-to-html

A PHP library to parse quill delta into html. All attributes and elements are based on a single listenere machnism. Therefore this makes this library highligh flexible. Adding your own listener to match a Quill plugin is easy to handle.

## Usage:

```php
$parser = new Parser('{
"ops": [
    {
    "insert": "Title"
    },
    {
    "attributes": {
        "header": 1
    },
    "insert": "\n"
    },
    {
    "insert": "Text with "
    },
    {
    "attributes": {
        "bold": true
    },
    "insert": "bold"
    },
    {
    "insert": " element.\n"
    }
]
}');

$parser->registerListener(new Heading());
$parser->registerListener(new Text());
$parser->registerListener(new Bold());

echo $parser->render();
```