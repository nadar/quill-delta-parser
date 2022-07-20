<?php
namespace nadar\quill\tests;

use nadar\quill\InlineListener;
use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\listener\Image;

class Issue55Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
   "ops":[
      {
         "insert":"Eye is a jewel of a town in North Suffolk with a wealth of interesting places to visit and friendly places to shop and eat. Dominated by its outstanding Victorian flint and brick "
      },
      {
         "attributes":{
            "bold":true
         },
         "insert":"Town Hall "
      },
      {
         "insert":"commissioned in 1857 by E B Lamb, the town of Eye was until the 1970s the smallest borough in the country. The Town is built around "
      },
      {
         "attributes":{
            "bold":true
         },
         "insert":"Eye Castle"
      },
      {
         "insert":" which possibly dates back to the eleventh century and is well worth a visit. You can take a leisurely walk around the town trail and visit some of the wonderful historic buildings. Recent spurts in population may see a step change in the town’s future development.\n"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"The magnificent fifteenth century Church of St Peter and St Paul is featured in the Churches and Chapels section of “"
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"Days Out in Suffolk"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"” available from "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"Mid Suffolk Tourist Information Centre"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"."
      },
      {
         "insert":"\n"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"Just beside the Church is the Guild Hall which again dates back to the 1400’s. The Guild’s activities could still be said to be alive represented by enthusiastic members of "
      },
      {
         "attributes":{
            "color":"#2d5c88",
            "background":"#ffffff",
            "link":"https://www.eyeartsguild.org.uk/"
         },
         "insert":"Eye Arts Guild"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":". "
      },
      {
         "attributes":{
            "underline":true,
            "italic":true,
            "color":"#666666",
            "bold":true
         },
         "insert":"Bararaq sei"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"."
      },
      {
         "insert":"\n"
      },
      {
         "attributes":{
            "style":"width: 136px;",
            "data-size":"200,200"
         },
         "insert":{
            "image":"/about_images/UITBM"
         }
      },
      {
         "attributes":{
            "align":"center"
         },
         "insert":"\n"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"The town is twinned with "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"Pouzauges"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":" in France and this promotes links to continental tourism. There is no official Tourist Office in Eye, although McColls newsagent and convenience store keeps an array of leaflets which are available to tourists including "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"“Eye Suffolk, A Tourist Guide”"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":", "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"“Heart of Suffolk Treasures”"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":", "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"“Eye Cycle Route” "
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"and the "
      },
      {
         "attributes":{
            "color":"#222222",
            "bold":true
         },
         "insert":"“Eye Town Trail”"
      },
      {
         "attributes":{
            "color":"#666666"
         },
         "insert":"."
      },
      {
         "insert":"\n"
      },
      {
         "insert":{
            "image":"/about_images/t3Cmt"
         }
      },
      {
         "attributes":{
            "align":"right"
         },
         "insert":"\n"
      }
   ]
}
JSON;

    public $html = <<<'EOT'
<p>Eye is a jewel of a town in North Suffolk with a wealth of interesting places to visit and friendly places to shop and eat. Dominated by its outstanding Victorian flint and brick <strong>Town Hall </strong>commissioned in 1857 by E B Lamb, the town of Eye was until the 1970s the smallest borough in the country. The Town is built around <strong>Eye Castle</strong> which possibly dates back to the eleventh century and is well worth a visit. You can take a leisurely walk around the town trail and visit some of the wonderful historic buildings. Recent spurts in population may see a step change in the town’s future development.</p>
<p><span style="color:#666666">The magnificent fifteenth century Church of St Peter and St Paul is featured in the Churches and Chapels section of “</span><span style="color:#222222"><strong>Days Out in Suffolk</strong></span><span style="color:#666666">” available from </span><span style="color:#222222"><strong>Mid Suffolk Tourist Information Centre</strong></span><span style="color:#666666">.</span></p>
<p><span style="color:#666666">Just beside the Church is the Guild Hall which again dates back to the 1400’s. The Guild’s activities could still be said to be alive represented by enthusiastic members of </span><a href="https://www.eyeartsguild.org.uk/" target="_blank"><span style="color:#2d5c88">Eye Arts Guild</span></a><span style="color:#666666">. </span><u><span style="color:#666666"><em><strong>Bararaq sei</strong></em></span></u><span style="color:#666666">.</span></p>
<p style="text-align: center;"><img src="/about_images/UITBM" alt="" class="img-responsive img-fluid" /></p>
<p><span style="color:#666666">The town is twinned with </span><span style="color:#222222"><strong>Pouzauges</strong></span><span style="color:#666666"> in France and this promotes links to continental tourism. There is no official Tourist Office in Eye, although McColls newsagent and convenience store keeps an array of leaflets which are available to tourists including </span><span style="color:#222222"><strong>“Eye Suffolk, A Tourist Guide”</strong></span><span style="color:#666666">, </span><span style="color:#222222"><strong>“Heart of Suffolk Treasures”</strong></span><span style="color:#222222"><strong>“Eye Cycle Route” </strong></span><span style="color:#666666">and the </span><span style="color:#222222"><strong>“Eye Town Trail”</strong></span><span style="color:#666666">.</span></p>
<p style="text-align: right;"><img src="/about_images/t3Cmt" alt="" class="img-responsive img-fluid" /></p>
EOT;

  public function getLexer()
  {
        return new Lexer($this->json);
  }

  public function listeners(Lexer $lexer)
  {
      $lexer->overwriteListener(new Image, new ImageSizeListener);
  }
}

class ImageSizeListener extends InlineListener {

    public $wrapper = '<img src="{src}" alt="" class="img-responsive img-fluid" />';

    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('image');
        $imageStyles = $line->getAttribute('style');

        if ($embedUrl) {
            $this->updateInput($line, str_replace(['{src}'], [$line->getLexer()->escape($embedUrl)], $this->wrapper));
        }

        if ($imageStyles) {
            $this->updateInput($line, str_replace(['{src}', '{styles}'], [$line->getLexer()->escape($embedUrl), $imageStyles], $this->wrapper));
        }
    }

}