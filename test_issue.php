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
$output = $lexer->render();
echo "Actual output:\n";
echo $output . "\n";
echo "\n---Expected---\n";
echo '<ul>
  <li>Item 1
    <ul>
      <li>Item 1.1
        <ul>
          <li>Item 1.1.1</li>
        </ul>
      </li>
    </ul>
  </li>
</ul>';
