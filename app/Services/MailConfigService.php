<?php

namespace App\Services;

use App\Mail\ContactLeadMail;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class MailConfigService
{
    /**
     * Resolve and decrypt the stored SMTP password unless a non-empty override is supplied.
     */
    public static function resolvePassword(?string $override = null): ?string
    {
        if (is_string($override) && $override !== '') {
            return $override;
        }

        $stored = Setting::get('mail_password');
        if (! is_string($stored) || $stored === '') {
            return null;
        }

        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable) {
            return $stored;
        }
    }

    /**
     * Parse one or more recipient emails separated by commas or semicolons.
     *
     * @return array<int, string>
     */
    public static function parseRecipients(?string $raw): array
    {
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $parts = preg_split('/[\s,;]+/', trim($raw)) ?: [];
        $valid = [];

        foreach ($parts as $email) {
            $email = trim($email);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            }
        }

        return array_values(array_unique($valid));
    }

    /**
     * Apply dynamic SMTP configuration from database settings (with optional form overrides).
     *
     * @return array<string, mixed>
     */
    public static function applyRuntimeConfig(array $overrides = []): array
    {
        $host = trim((string) ($overrides['mail_host'] ?? Setting::get('mail_host', '')));
        $port = (int) ($overrides['mail_port'] ?? Setting::get('mail_port', 587));
        $encryption = strtolower(trim((string) ($overrides['mail_encryption'] ?? Setting::get('mail_encryption', 'tls'))));
        $username = trim((string) ($overrides['mail_username'] ?? Setting::get('mail_username', '')));
        $password = static::resolvePassword($overrides['mail_password'] ?? null);

        $fromAddress = trim((string) ($overrides['mail_from_address'] ?? Setting::get('mail_from_address', '')));
        if ($fromAddress === '' && filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $fromAddress = $username;
        }
        if ($fromAddress === '') {
            $fromAddress = (string) config('mail.from.address', 'hello@example.com');
        }

        $fromName = trim((string) ($overrides['mail_from_name'] ?? Setting::get('mail_from_name', 'Norm Studio')));
        if ($fromName === '') {
            $fromName = 'Norm Studio';
        }

        $toRaw = (string) ($overrides['mail_to_address'] ?? Setting::get('mail_to_address', ''));
        if (trim($toRaw) === '') {
            $toRaw = (string) Setting::get('email', '');
        }
        $recipients = static::parseRecipients($toRaw);

        // Laravel 12 uses 'smtps' for implicit SSL (typically port 465) and 'smtp' for STARTTLS / plain SMTP.
        $scheme = ($encryption === 'ssl' || ($encryption === '' && $port === 465)) ? 'smtps' : 'smtp';

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'scheme' => $scheme,
                'url' => null,
                'host' => $host,
                'port' => $port > 0 ? $port : 587,
                'username' => $username !== '' ? $username : null,
                'password' => $password,
                'timeout' => 15,
                'local_domain' => parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost',
            ],
            'mail.from' => [
                'address' => $fromAddress,
                'name' => $fromName,
            ],
        ]);

        Mail::purge('smtp');

        return [
            'host' => $host,
            'port' => $port > 0 ? $port : 587,
            'scheme' => $scheme,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'recipients' => $recipients,
        ];
    }

    /**
     * Send contact notification email if enabled and configured in settings.
     */
    public static function sendContactNotification(Contact $contact): bool
    {
        $enabled = filter_var(Setting::get('mail_enabled', false), FILTER_VALIDATE_BOOLEAN);
        if (! $enabled) {
            return false;
        }

        $resolved = static::applyRuntimeConfig();
        if ($resolved['host'] === '' || empty($resolved['recipients'])) {
            return false;
        }

        Mail::to($resolved['recipients'])->send(new ContactLeadMail($contact));

        return true;
    }
}

