<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;
use nadar\quill\Uri;

/**
 * Convert Image attributes into image element.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.2
 */
class Image extends InlineListener
{
    /**
     * @var string
     */
    public $wrapper = '<img src="{src}" {width} {height} alt="" class="img-responsive img-fluid" />';

    /**
     * @var array<string> The list of allowed URI schemes for the `src` attribute. Values with a
     * scheme which is not part of this allowlist (like `javascript:`) are not rendered at all,
     * otherwise this would allow cross-site scripting (XSS). URIs without any scheme (relative
     * paths) are always allowed and can not be removed from validation.
     * @since 3.7.1
     */
    public $safeSchemes = ['http', 'https', 'data'];

    /**
     * @var array<string> When the `data` scheme is allowed (see {@see $safeSchemes}), only data URIs
     * with these media types are rendered (for example `data:image/png;base64,...`). This prevents
     * payloads like `data:text/html,...`.
     * @since 3.7.1
     */
    public $dataMediaTypes = ['image'];

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('image');
        if ($embedUrl) {
            if (!$this->isSafeSource($embedUrl)) {
                // never render untrusted uri schemes into the src attribute
                $line->debugInfo('Image source "' . $embedUrl . '" has been blocked due to an unsafe uri scheme.');
                $this->updateInput($line, '');
                return;
            }

            if ($width = $line->getAttribute('width')) {
                $width = 'width="'.$line->getLexer()->escape($width).'"';
            }

            if ($height = $line->getAttribute('height')) {
                $height = 'height="'.$line->getLexer()->escape($height).'"';
            }

            $this->updateInput($line, preg_replace('#\s+#', ' ', str_replace([
                '{src}',
                '{width}',
                '{height}'
            ], [
                $line->getLexer()->escape($embedUrl),
                $width,
                $height
            ], $this->wrapper)));
        }
    }

    /**
     * Whether the given image source passes the scheme allowlist validation.
     *
     * @param mixed $url
     * @return boolean
     */
    protected function isSafeSource($url)
    {
        if (!Uri::isSafe($url, $this->safeSchemes)) {
            return false;
        }

        // even when data uris are allowed, only render the configured media types,
        // so data:image/png passes while data:text/html or data:application/xhtml+xml do not.
        if (Uri::getScheme($url) === 'data') {
            $mediaType = Uri::getDataMediaType($url);

            foreach ($this->dataMediaTypes as $allowedType) {
                if ($mediaType !== false && strpos($mediaType, strtolower($allowedType) . '/') === 0) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }
}
