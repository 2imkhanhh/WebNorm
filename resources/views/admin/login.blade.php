<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị | Norm Studio</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #0b0c0e;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(253, 255, 142, 0.05) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(255, 255, 255, 0.03) 0%, transparent 25%);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #14161a;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 44px 36px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 48px;
            height: auto;
            margin-bottom: 16px;
            filter: drop-shadow(0 2px 8px rgba(255, 255, 255, 0.1));
        }

        .brand-title {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #8a8f98;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-status {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #c4c7cc;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            color: #646a73;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            background: #1c1f24;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 13px 14px 13px 42px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #FDFF8E;
            background: #22262c;
            box-shadow: 0 0 0 3px rgba(253, 255, 142, 0.15);
        }

        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 26px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #8a8f98;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            accent-color: #FDFF8E;
            width: 15px;
            height: 15px;
        }

        .btn-submit {
            width: 100%;
            background: #FDFF8E;
            color: #111111;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #f8fb70;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(253, 255, 142, 0.25);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #8a8f98;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .back-link a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Norm" class="brand-logo">
            <h1 class="brand-title">Website Studio</h1>
            <p class="brand-subtitle">Đăng nhập tài khoản quản trị viên</p>
        </div>

        @if(session('status'))
            <div class="alert-status">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <i class='bx bx-error-circle'></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email quản trị</label>
                <div class="input-wrap">
                    <i class='bx bx-envelope'></i>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', 'admin@norm.vn') }}" required autofocus placeholder="admin@norm.vn">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-wrap">
                    <i class='bx bx-lock-alt'></i>
                    <input type="password" id="password" name="password" class="form-control" value="admin123" required placeholder="••••••••">
                </div>
            </div>

            <div class="form-meta">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" checked>
                    <span>Ghi nhớ đăng nhập</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <span>Vào trang quản trị</span>
                <i class='bx bx-right-arrow-alt'></i>
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('home') }}">
                <i class='bx bx-arrow-back'></i> Quay lại trang chủ Norm
            </a>
        </div>
    </div>
</body>
</html>

