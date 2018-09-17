<?php

use nadar\quill\Parser;
require 'vendor/autoload.php';
?>

<form method="post">
<textarea name="delta" rows="40" cols="50"></textarea>
</form>

<?php
$parser = new Parser(isset($_PSOT['delta']) ? $_POST['delta'] : null);
$parser->initBuiltInListeners();
$output = $parser->render();
?>
<div style="border:1px solid red; padding:20p;">
<?= $output; ?>
</div>

<?php var_dump($output); ?>