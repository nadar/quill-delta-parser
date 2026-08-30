<?php

namespace nadar\quill;

/**
 * URI helpers to validate values which are written into HTML attributes.
 *
 * Delta attributes like `link`, `image` or `video` contain URIs which end up in
 * `href` or `src` HTML attributes. Those values are usually attacker controlled
 * (user generated content), therefore only a limited set of URI schemes can be
 * considered safe. Otherwise values like `javascript:alert(1)` would be written
 * verbatim into the rendered markup and lead to cross-site scripting (XSS).
 *
 * @since 3.6.0
 * @author Basil Suter <basil@nadar.io>
 */
class Uri
{
    /**
     * @var array<string> The default list of allowed URI schemes. Scheme-less (relative)
     * URIs are always considered safe and can not be removed from this list.
     */
    public const SAFE_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /**
     * Whether the given value is a URI which is safe to write into an HTML attribute.
     *
     * A value is considered safe when it either has no scheme at all (relative paths,
     * anchors like `#foo` or protocol-relative URLs like `//example.com`) or when its
     * scheme is part of the given allowlist.
     *
     * @param mixed $uri The URI from a delta attribute, usually a string.
     * @param array<string> $allowedSchemes An array with lowercase scheme names without trailing colon.
     * @return boolean Whether the URI is safe for output or not.
     */
    public static function isSafe($uri, array $allowedSchemes = self::SAFE_SCHEMES)
    {
        if (!is_string($uri)) {
            // values which are not strings can not be validated and are therefore considered unsafe.
            return false;
        }

        $scheme = self::getScheme($uri);

        // URIs without any scheme are relative and therefore considered safe,
        // as they are resolved against the current document by the browser.
        if ($scheme === false) {
            return true;
        }

        return in_array($scheme, $allowedSchemes, true);
    }

    /**
     * Get the lowercase scheme of the given URI.
     *
     * Browser specific normalization is applied before detecting the scheme: browsers
     * ignore ASCII control characters (including tab, newline and space) when parsing
     * URLs, so those are stripped prior to extraction - otherwise payloads like
     * `java&#9;script:` would not be detected as `javascript` scheme. The comparison
     * itself is case insensitive, so `JaVaScRiPt:` is detected as well.
     *
     * @param mixed $uri The URI from a delta attribute, usually a string.
     * @return string|boolean The normalized scheme (without trailing colon) or false if the
     * URI has no scheme (or is not a string).
     */
    public static function getScheme($uri)
    {
        if (!is_string($uri)) {
            return false;
        }

        // strip ASCII control chars, whitespace and DEL from everywhere in the value,
        // this mirrors what browsers do before parsing the scheme but errs on the
        // safe side for characters browsers would only trim at the edges.
        $normalized = preg_replace('~[\x00-\x20\x7f]+~', '', $uri);

        if ($normalized === null || !preg_match('~^([a-z][a-z0-9+.\-]*):~i', $normalized, $match)) {
            return false;
        }

        return strtolower($match[1]);
    }

    /**
     * Get the media type of a data URI.
     *
     * For example `data:image/png;base64,...` returns `image/png`. If the URI is not
     * a data URI or contains no explicit media type, false is returned.
     *
     * @param mixed $uri The URI from a delta attribute, usually a string.
     * @return string|boolean The lowercase media type or false if not detectable.
     */
    public static function getDataMediaType($uri)
    {
        if (!is_string($uri) || self::getScheme($uri) !== 'data') {
            return false;
        }

        $normalized = preg_replace('~[\x00-\x20\x7f]+~', '', $uri);

        if ($normalized === null || !preg_match('~^data:([^;,]+)[;,]~i', $normalized, $match)) {
            return false;
        }

        return strtolower($match[1]);
    }
}
