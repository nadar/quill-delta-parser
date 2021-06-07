<?php

namespace nadar\quill\tests;

/**
 * Code Block Testing
 */
class CodeBlockListenerTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [{"insert": "for (int i = 7000000; i < 7999999; i++) { Console.WriteLine(\"Number: +960 \" + i); StringBuilder sb = new StringBuilder(); sb.AppendFormat(\"\\n{0},{1}\", \"Name\", i); File.AppendAllText(\"Dhiraagu.csv\", sb.ToString()); }"}, {"insert": "\n", "attributes": {"code-block": true}}]}

JSON;

    public $html = '<pre><code>for (int i = 7000000; i &lt; 7999999; i++) { Console.WriteLine(&quot;Number: +960 &quot; + i); StringBuilder sb = new StringBuilder(); sb.AppendFormat(&quot;\n{0},{1}&quot;, &quot;Name&quot;, i); File.AppendAllText(&quot;Dhiraagu.csv&quot;, sb.ToString()); }</code></pre>';
}
