<?php

namespace App\Helpers;

/**
 * Normalizes the `user.social_media` JSON column for public display.
 *
 * Teachers type anything into their profile: a full URL, "www.instagram.com/x",
 * a handle like "@x", or nothing. The frontend puts the value straight into an
 * <a href>, so a bare handle becomes a broken relative link and a
 * "javascript:" value would be an XSS vector. Every value is turned into an
 * https URL on the platform's own domain, or null.
 */
class SocialMediaHelper
{
    /** Platform => [allowed host suffixes, profile URL template for a handle]. */
    private const PLATFORMS = [
        'linkedin' => [['linkedin.com'], 'https://www.linkedin.com/in/%s'],
        'facebook' => [['facebook.com', 'fb.com'], 'https://www.facebook.com/%s'],
        'instagram' => [['instagram.com'], 'https://www.instagram.com/%s'],
    ];

    /**
     * @param  string|array|object|null  $raw  JSON string from the DB, or an already decoded value
     * @return array{linkedin: ?string, facebook: ?string, instagram: ?string}
     */
    public static function normalize(mixed $raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (is_object($raw)) {
            $raw = (array) $raw;
        }
        if (!is_array($raw)) {
            $raw = [];
        }

        $result = [];
        foreach (self::PLATFORMS as $platform => [$hosts, $template]) {
            $value = $raw[$platform] ?? null;
            $result[$platform] = is_string($value) ? self::toUrl($value, $hosts, $template) : null;
        }

        return $result;
    }

    private static function toUrl(string $value, array $hosts, string $template): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Full URL, or a URL typed without a scheme ("www.instagram.com/x", "instagram.com/x").
        $looksLikeUrl = str_contains($value, '://') || preg_match('~^(www\.)?[a-z0-9-]+(\.[a-z0-9-]+)+/~i', $value);
        if ($looksLikeUrl) {
            if (!str_contains($value, '://')) {
                $value = 'https://' . $value;
            }

            $parts = parse_url($value);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            if (!in_array($scheme, ['http', 'https'], true) || $host === '' || !self::hostAllowed($host, $hosts)) {
                return null;
            }

            return 'https://' . $host . ($parts['path'] ?? '') . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }

        // Plain handle: "@name", "name", "name/".
        $handle = trim(ltrim($value, '@'), '/');
        if (!preg_match('/^[A-Za-z0-9._-]{1,100}$/', $handle)) {
            return null;
        }

        return sprintf($template, $handle);
    }

    private static function hostAllowed(string $host, array $allowed): bool
    {
        foreach ($allowed as $domain) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }
}
