<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $settings['mail_password_set'] = ! empty($settings['mail_password']);
        $settings['mail_password'] = '';

        return Inertia::render('Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $items = $request->input('settings', []);
        if (! is_array($items)) {
            return back()->withErrors(['settings' => 'Dữ liệu cài đặt không hợp lệ.']);
        }

        foreach ($items as $key => $value) {
            if (! in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            if ($key === 'mail_password') {
                if (! is_string($value) || $value === '') {
                    continue;
                }
                $value = Crypt::encryptString($value);
            }

            if ($key === 'mail_enabled') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Đã lưu cấu hình cài đặt website thành công!');
    }

    public function testMail(Request $request)
    {
        $overrides = $request->only([
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_from_name',
            'mail_to_address',
        ]);

        try {
            $resolved = MailConfigService::applyRuntimeConfig($overrides);

            if ($resolved['host'] === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập SMTP Host trước khi gửi thử.',
                ], 422);
            }

            if (empty($resolved['recipients'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập Email nhận thông báo hợp lệ.',
                ], 422);
            }

            Mail::html(
                '<p>Đây là email kiểm tra cấu hình SMTP từ trang quản trị <strong>Norm Studio</strong>.</p><p>Nếu bạn nhận được email này, cấu hình gửi mail liên hệ đã hoạt động chính xác.</p>',
                function ($message) use ($resolved) {
                    $message->to($resolved['recipients'])
                        ->subject('[Norm] Kiểm tra cấu hình SMTP thành công');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi email thử nghiệm tới: ' . implode(', ', $resolved['recipients']),
            ]);
        } catch (\Throwable $e) {
            Log::warning('SMTP test mail failed: ' . $e->getMessage());

            $errorDetail = config('app.debug')
                ? $e->getMessage()
                : 'Không thể kết nối máy chủ SMTP. Vui lòng kiểm tra Host, Port, Email và Mật khẩu ứng dụng.';

            return response()->json([
                'success' => false,
                'message' => 'Gửi thử thất bại: ' . $errorDetail,
            ], 500);
        }
    }
}
