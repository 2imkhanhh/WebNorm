<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NormHelper
{
    public static function locale(?string $lang = null): string
    {
        $resolved = $lang ?: (app()->getLocale() ?: session('locale', 'vi'));
        return in_array($resolved, ['vi', 'en'], true) ? $resolved : 'vi';
    }

    /**
     * Determine whether the current request is an authenticated draft preview.
     */
    public static function isDraftPreview(?Request $request = null): bool
    {
        $req = $request ?: request();
        return auth()->check() && $req->query('preview') === 'draft';
    }

    /**
     * Resolve named client route for the active (or specified) language.
     */
    public static function localizedRoute(string $name, mixed $parameters = [], ?string $lang = null): string
    {
        $targetLang = static::locale($lang);
        $routeName = ($targetLang === 'en' && Route::has('en.' . $name))
            ? 'en.' . $name
            : $name;

        $url = route($routeName, $parameters);

        $query = (string) parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);
        if (static::isDraftPreview() && !array_key_exists('preview', $queryParams)) {
            $url .= ($query !== '' ? '&' : '?') . 'preview=draft';
        }

        return $url;
    }

    /**
     * Localize an internal relative URL (e.g. from Admin link fields) for the active language.
     */
    public static function localizeUrl(?string $url, ?string $lang = null): string
    {
        $value = trim((string) $url);
        if ($value === '' || $value === '#') {
            return $value === '' ? static::localizedRoute('home', [], $lang) : $value;
        }

        if (preg_match('#^(https?:|//|mailto:|tel:|javascript:)#i', $value)) {
            return $value;
        }

        $parts = parse_url($value);
        $path = '/' . ltrim($parts['path'] ?? '/', '/');
        $basePath = preg_replace('#^/en(?=/|$)#', '', $path) ?: '/';
        $targetLang = static::locale($lang);
        $targetPath = $targetLang === 'en'
            ? ($basePath === '/' ? '/en' : '/en' . $basePath)
            : $basePath;

        $queryParams = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $queryParams);
        }
        if (static::isDraftPreview() && !array_key_exists('preview', $queryParams)) {
            $queryParams['preview'] = 'draft';
        }

        $result = url($targetPath);
        if (!empty($queryParams)) {
            $result .= '?' . http_build_query($queryParams);
        }
        if (!empty($parts['fragment'])) {
            $result .= '#' . $parts['fragment'];
        }

        return $result;
    }

    /**
     * Build the equivalent URL of the current request for another locale.
     */
    public static function switchLocaleUrl(string $targetLocale, ?Request $request = null, bool $preserveQuery = true): string
    {
        $req = $request ?: request();
        $targetLang = $targetLocale === 'en' ? 'en' : 'vi';

        $path = '/' . ltrim($req->path(), '/');
        $basePath = preg_replace('#^/en(?=/|$)#', '', $path) ?: '/';

        $targetPath = $targetLang === 'en'
            ? ($basePath === '/' ? '/en' : '/en' . $basePath)
            : $basePath;

        $url = url($targetPath);

        if ($preserveQuery) {
            $query = $req->query();
            unset($query['lang']);
            if (!static::isDraftPreview($req)) {
                unset($query['preview']);
            }
            if (!empty($query)) {
                $url .= '?' . http_build_query($query);
            }
        }

        return $url;
    }

    /**
     * Build clean canonical/alternate URL without tracking or preview query parameters.
     */
    public static function canonicalUrl(?string $lang = null, ?Request $request = null): string
    {
        $targetLang = static::locale($lang);
        return static::switchLocaleUrl($targetLang, $request, false);
    }

    /**
     * Get translated text from array, json string, or plain string.
     */
    public static function val($value, string $default = '', ?string $lang = null): string
    {
        $currentLang = static::locale($lang);

        if (is_null($value)) {
            return $default;
        }

        if (is_array($value)) {
            $picked = $value[$currentLang] ?? null;
            if ($picked === null || $picked === '') {
                $picked = $value['vi'] ?? ($value['en'] ?? reset($value));
            }
            return ($picked !== null && $picked !== false && $picked !== '') ? (string) $picked : $default;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return static::val($decoded, $default, $currentLang);
            }
            return $value !== '' ? $value : $default;
        }

        return (string) $value;
    }

    /**
     * Get setting value by key with fallback.
     */
    public static function setting(string $key, $default = '', ?string $lang = null)
    {
        if (static::isDraftPreview() && $key !== 'mail_password' && $key !== 'mail_password_encrypted') {
            $draft = \App\Services\DraftService::getDraft('settings');
            $draftSettings = $draft?->payload['settings'] ?? null;
            if (is_array($draftSettings) && array_key_exists($key, $draftSettings)) {
                $draftVal = $draftSettings[$key];
                if ($draftVal === null) {
                    return static::val($default, (string) $default, $lang);
                }
                return static::val($draftVal, (string) $default, $lang);
            }
        }

        $val = Setting::get($key, null);
        if ($val === null) {
            return static::val($default, (string) $default, $lang);
        }
        return static::val($val, (string) $default, $lang);
    }

    /**
     * Strip unsafe executable tags, inline event handlers, and javascript: URIs from rich HTML.
     */
    public static function sanitizeHtml(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $clean = preg_replace('/<(script|iframe|object|embed|svg|form|meta|base)\b[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $clean = preg_replace('/<(script|iframe|object|embed|svg|form|meta|base)\b[^>]*\/?>/is', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/is', '', $clean) ?? $clean;
        $clean = preg_replace('/(href|src)\s*=\s*(["\'])\s*(?:javascript|vbscript|data\s*:\s*text\/html)\s*:[^"\']*\2/is', '$1="#"', $clean) ?? $clean;

        return $clean;
    }
}
