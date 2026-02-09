<?php
require __DIR__ . '/vendor/autoload.php';

// Exact example from issue
$json = '{
  "ops": [
    { "insert": "Item 1\n", "attributes": { "list": "bullet" } }
  ]
}';

$lexer = new \nadar\quill\Lexer($json);
$lexer->debugEnabled = true;
$output = $lexer->render();

// Use Debug class
$debug = new \nadar\quill\Debug($lexer);
echo $debug->debugPrint();
