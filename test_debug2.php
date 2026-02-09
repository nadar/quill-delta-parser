<?php
require __DIR__ . '/vendor/autoload.php';

// Exact example from issue
$json = '{
  "ops": [
    { "insert": "Item 1\n", "attributes": { "list": "bullet" } },
    { "insert": "Item 1.1\n", "attributes": { "list": "bullet", "indent": 1 } },
    { "insert": "Item 1.1.1\n", "attributes": { "list": "bullet", "indent": 2 } }
  ]
}';

$lexer = new \nadar\quill\Lexer($json);
$lexer->debugEnabled = true;
$output = $lexer->render();

// Use Debug class
$debug = new \nadar\quill\Debug($lexer);
echo $debug->debugPrint();
