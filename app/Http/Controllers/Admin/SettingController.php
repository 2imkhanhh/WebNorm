<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Whitelist of permitted setting keys persisted from Admin Settings.
     */
    private const ALLOWED_KEYS = [
        'logo',
        'model_3d',
        'copyright',
        'footer_btn_text',
        'footer_social_title',
        'footer_line1_old',
        'footer_line1_new',
        'footer_line2_old',
        'footer_line2_new',
        'footer_line3_old',
        'footer_line3_new',
        'hotline',
        'email',
        'address',
        'mail_enabled',
        'mail_to_address',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
        'social_facebook',
        'social_instagram',
        'social_linkedin',
        'social_behance',
        'social_dribbble',
    ];

    public function index()
    {
        $allSettings = Setting::all();
        $settings = [];
        foreach ($allSettings as $s) {
            $settings[$s->key] = $s->value;
        }

        $hasPassword = ! empty($settings['mail_password']);
        $draft = DraftService::getDraft('settings');
        $hasDraft = false;
        $draftUpdatedAt = null;

        if ($draft && is_array($draft->payload)) {
            $hasDraft = true;
            $draftUpdatedAt = $draft->updated_at?->toIso8601String();
            $draftItems = $draft->payload['settings'] ?? [];
            foreach ($draftItems as $k => $v) {
                if ($k === 'mail_password_encrypted') {
                    if (! empty($v)) {
                        $hasPassword = true;
                    }
                    continue;
                }
                if (in_array($k, self::ALLOWED_KEYS, true)) {
                    $settings[$k] = $v;
                }
            }
        }

        $settings['mail_password_set'] = $hasPassword;
        $settings['mail_password'] = '';

        return Inertia::render('Settings', [
            'settings' => $settings,
            'hasDraft' => $hasDraft,
            'draftUpdatedAt' => $draftUpdatedAt,
        ]);
    }

    public function saveDraft(Request $request)
    {
        $items = $request->input('settings', []);
        if (! is_array($items)) {
            return back()->withErrors(['settings' => 'Dữ liệu cài đặt không hợp lệ.']);
        }

        $existingDraft = DraftService::getDraft('settings');
        $existingEncryptedPassword = $existingDraft?->payload['settings']['mail_password_encrypted'] ?? null;

        $draftSettings = [];
        foreach ($items as $key => $value) {
            if (! in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            if ($key === 'mail_password') {
                if (is_string($value) && $value !== '') {
                    $draftSettings['mail_password_encrypted'] = Crypt::encryptString($value);
                } elseif (! empty($existingEncryptedPassword)) {
                    $draftSettings['mail_password_encrypted'] = $existingEncryptedPassword;
                }
                continue;
            }

            if ($key === 'mail_enabled') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $draftSettings[$key] = $value;
        }

        DraftService::saveDraft('settings', ['settings' => $draftSettings]);

        return back()->with('success', 'Đã lưu bản nháp cài đặt website thành công!');
    }

    public function discardDraft()
    {
        DraftService::discardDraft('settings');

        return back()->with('success', 'Đã xóa bản nháp cài đặt, quay về cấu hình đã xuất bản!');
    }

    public function update(Request $request)
    {
        $items = $request->input('settings', []);
        if (! is_array($items)) {
            return back()->withErrors(['settings' => 'Dữ liệu cài đặt không hợp lệ.']);
        }

        $existingDraft = DraftService::getDraft('settings');
        $draftEncryptedPassword = $existingDraft?->payload['settings']['mail_password_encrypted'] ?? null;

        foreach ($items as $key => $value) {
            if (! in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            if ($key === 'mail_password') {
                if (is_string($value) && $value !== '') {
                    $value = Crypt::encryptString($value);
                } elseif (! empty($draftEncryptedPassword)) {
                    $value = $draftEncryptedPassword;
                } else {
                    continue;
                }
            }

            if ($key === 'mail_enabled') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        DraftService::discardDraft('settings');

        return back()->with('success', 'Đã lưu cấu hình cài đặt website thành công!');
    }
}
