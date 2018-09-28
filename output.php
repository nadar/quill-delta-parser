<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use nadar\quill\Parser;
use nadar\quill\Lexer;
use nadar\quill\Debug;

require 'vendor/autoload.php';

$json = isset($_POST['quill-editor-input']) ? $_POST['quill-editor-input'] : '{}';

$lex = new Lexer($json);
$html = $lex->render();
$debuger = new Debug($lex);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
<body>
<form method="post" class="demo">
    <input type="hidden" id="quill-editor-input" name="quill-editor-input" />
    <div id="editor" style="height:100px;">
    </div>
    <input type="submit" name="submit" value="Parse" class="btn btn-primary" />
</form>
<!-- OUTPUT TESTER-->
<div style="border:1px solid blue; padding:20px; margin-top:20px;"><pre>
<?= $json; ?>
</pre></div>
<?php ?>
<div style="border:1px solid red; padding:20px; margin-top:20px;"><pre>
<?= $html; ?>
</pre></div>
<div style="border:1px solid green; padding:20px; margin-top:20px;">
<?= htmlentities($html, ENT_QUOTES); ?>
</div>
<div style="border:1px solid green; padding:20px; margin-top:20px;">
<?= $debuger->debugPrint(); ?>
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
            ['clean']
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