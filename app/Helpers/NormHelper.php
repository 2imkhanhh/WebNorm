<?php

namespace App\Helpers;

use App\Models\Setting;

class NormHelper
{
    /**
     * Get translated text from array, json string, or plain string.
     */
    public static function val($value, string $default = '', string $lang = 'vi'): string
    {
        if (is_null($value)) {
            return $default;
        }

        if (is_array($value)) {
            return (string) ($value[$lang] ?? $value['vi'] ?? reset($value) ?? $default);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return (string) ($decoded[$lang] ?? $decoded['vi'] ?? reset($decoded) ?? $default);
            }
            return $value;
        }

        return (string) $value;
    }

    /**
     * Get setting value by key with fallback.
     */
    public static function setting(string $key, $default = '', string $lang = 'vi')
    {
        $val = Setting::get($key, $default);
        return static::val($val, (string) $default, $lang);
    }
}

