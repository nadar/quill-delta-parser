<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use nadar\quill\Lexer;
use nadar\quill\Debug;

require 'vendor/autoload.php';

$json = isset($_POST['quill-editor-input']) ? $_POST['quill-editor-input'] : '{}';
$jsonOptions = JSON_PRETTY_PRINT;
$lex = new Lexer($json);
$lex->debug = true;
$html = $lex->render();
$debuger = new Debug($lex);

if (isset($_POST['unittest'])) {
    $class = '
<?php
namespace nadar\quill\tests;

class '.$_POST['className'].' extends DeltaTestCase
{
    public $json = <<<\'JSON\'
'.$json.'
JSON;

    public $html = <<<\'EOT\'
'.$html.'
EOT;
}
';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
<body>
<div class="p-3">
    <form method="post" class="demo">
        <div class="form-group">
            <input type="text" name="className" value="XyzTest" class="form-control" />
        </div>
        <input type="hidden" id="quill-editor-input" name="quill-editor-input" />
        <div id="editor" class="mb-2" style="height:150px;"></div>
        <input type="submit" name="submit" value="Parse" class="btn btn-primary" />
        <input type="submit" name="unittest" value="Create unit Test" class="btn btn-secondary" />
    </form>
    <?php if (isset($class)): ?>
    <div class="alert alert-info mt-3">
        <div class="form-group">
            <textarea class="small mt-3 form-control" ols="30" rows="10"><?= $class; ?></textarea>
        </div>
    </div>
    <?php endif; ?>
    <!-- OUTPUT TESTER-->
    <?php if (isset($_POST['quill-editor-input'])): ?>
        <div class="mt-3">
            <p class="lead">Delta Json</p>
            <pre class="border small p-2"><code><?= json_encode(json_decode($json), $jsonOptions); ?></code></pre>
            <p class="lead">RAW Html Output</p>
            <pre class="border p-2"><code><?= htmlentities($html, ENT_QUOTES); ?></code></pre>
            <p class="lead">HTML</p>
            <pre class="border p-2"><code><?= $html; ?></code></pre>
            <p class="text-muted small">The above HTML output is formatted using Bootstrap 4 styles, therefore paddings and margins may vary to your output.</p>
        </div>
    <hr />
    <?= $debuger->debugPrint(); ?>
    <?php endif; ?>
</div>
<!-- Initialize Quill editor -->
<script>
var editor = new Quill('#editor', {
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike', 'blockquote'],
            ['link'],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['image', 'video'],
            [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }]

        ]  
    },
    theme: 'snow'
});
$('form.demo').submit(function() {
    $('#quill-editor-input').val(JSON.stringify(editor.getContents()));
    return true;
});
editor.setContents(<?= $json; ?>);
</script>
</body>
</html>