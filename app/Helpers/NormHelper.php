<?php

namespace App\Helpers;

use App\Models\Setting;

class NormHelper
{
    public static function locale(?string $lang = null): string
    {
        $resolved = $lang ?: (app()->getLocale() ?: session('locale', 'vi'));
        return in_array($resolved, ['vi', 'en'], true) ? $resolved : 'vi';
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
        $val = Setting::get($key, null);
        if ($val === null) {
            return static::val($default, (string) $default, $lang);
        }
        return static::val($val, (string) $default, $lang);
    }
}

