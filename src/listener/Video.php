<?php

namespace nadar\quill\listener;

use nadar\quill\BlockListener;
use nadar\quill\Line;
use nadar\quill\Uri;

/**
 * Convert Video attributes into tags.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Video extends BlockListener
{
    /**
     * @var array<string> Allow options for iframe allow param
     * @since 2.5.0
     */
    public $allow = ['accelerometer', 'autoplay', 'encrypted-media', 'gyroscope', 'picture-in-picture'];

    /**
     * @var array<string> The list of allowed URI schemes for the iframe `src` attribute. Values with a
     * scheme which is not part of this allowlist (like `javascript:`) are not rendered at all, otherwise
     * this would allow cross-site scripting (XSS). URIs without any scheme (relative paths) are always
     * allowed and can not be removed from validation.
     * @since 3.5.1
     */
    public $safeSchemes = ['http', 'https'];

    /**
     * @var string The wrapper template which is taken to generate the video element.
     */
    public $wrapper = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="{url}" frameborder="0" allow="{allow}" allowfullscreen></iframe></div>'.PHP_EOL;

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('video');
        if ($embedUrl) {
            if (!Uri::isSafe($embedUrl, $this->safeSchemes)) {
                // never render untrusted uri schemes into the src attribute
                $line->debugInfo('Video source "' . $embedUrl . '" has been blocked due to an unsafe uri scheme.');
                $line->output = '';
                $line->setDone();
                return;
            }

            $line->output = str_replace(['{url}', '{allow}'], [$line->getLexer()->escape($embedUrl), implode("; ", $this->allow)], $this->wrapper);
            $line->setDone();
        }
    }
}
