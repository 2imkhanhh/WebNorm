<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo liên hệ mới</title>
</head>
<body style="margin:0;padding:24px;background-color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
        <tr>
            <td style="padding:20px 24px;background:#0f172a;color:#ffffff;">
                <h2 style="margin:0;font-size:18px;font-weight:700;">Yêu cầu liên hệ mới từ website</h2>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                    <tr>
                        <td style="padding:8px 0;width:150px;color:#64748b;vertical-align:top;">Khách hàng</td>
                        <td style="padding:8px 0;font-weight:600;color:#0f172a;">{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Email</td>
                        <td style="padding:8px 0;border-top:1px solid #f1f5f9;">
                            <a href="mailto:{{ $contact->email }}" style="color:#2563eb;text-decoration:none;">{{ $contact->email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Số điện thoại</td>
                        <td style="padding:8px 0;border-top:1px solid #f1f5f9;">{{ $contact->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Dịch vụ quan tâm</td>
                        <td style="padding:8px 0;border-top:1px solid #f1f5f9;">
                            @php
                                $svcs = is_array($contact->services) ? implode(', ', array_filter($contact->services)) : ($contact->services ?: '');
                            @endphp
                            {{ $svcs !== '' ? $svcs : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Ngân sách dự kiến</td>
                        <td style="padding:8px 0;border-top:1px solid #f1f5f9;">{{ $contact->budget ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Nội dung</td>
                        <td style="padding:8px 0;border-top:1px solid #f1f5f9;white-space:pre-line;line-height:1.5;">{{ $contact->message ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;border-top:1px solid #f1f5f9;">Thời gian gửi</td>
                        <td style="padding:8px 0;color:#64748b;border-top:1px solid #f1f5f9;">{{ $contact->created_at?->format('H:i d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

