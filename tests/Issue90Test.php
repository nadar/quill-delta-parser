<?php

namespace nadar\quill\tests;

class Issue87Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":
    [
        {"insert":"1"},
        {"attributes":{"list":"ordered"},"insert":"\n"},
        {"insert":"3"},
        {"attributes":{"indent":1,"align":"right","list":"ordered"},"insert":"\n"},
        {"insert":"4"},
        {"attributes":{"list":"ordered"},"insert":"\n"},
        {"insert":"5"},
        {"attributes":{"align":"center","list":"ordered"},"insert":"\n"},
        {"insert":"6"},
        {"attributes":{"list":"ordered"},"insert":"\n"},
        {"attributes":{"underline":true},"insert":"Hello"},
        {"attributes":{"list":"bullet"},"insert":"\n"},
        {"attributes":{"italic":true},"insert":"Please"},
        {"attributes":{"align":"center","list":"bullet"},"insert":"\n"},
        {"attributes":{"bold":true},"insert":"Work"},
        {"attributes":{"align":"right","list":"bullet"},"insert":"\n"}
    ]
}
JSON;

    public $html = <<<'EOT'
    <ol><li>1</li><li style="text-align: right;">3</li><li>4</li><li style="text-align: center;">5</li><li>6</li></ol><ul><li><u>Hello</u></li><li style="text-align: center;"><em>Please</em></li><li style="text-align: right;"><strong>Work</strong></li></ul>
EOT;
}