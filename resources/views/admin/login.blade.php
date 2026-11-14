<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị | Norm Studio</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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

        .brand-logo-wrap {
            width: 60px;
            height: 60px;
            background: #ffffff;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.12);
        }

        .brand-logo {
            width: 36px;
            height: auto;
            display: block;
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

        .input-wrap .input-icon-left {
            position: absolute;
            left: 14px;
            color: #646a73;
            font-size: 18px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: #1c1f24;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 13px 42px 13px 42px;
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

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #8a8f98;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: #ffffff;
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
            <div class="brand-logo-wrap">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Norm" class="brand-logo">
            </div>
            <h1 class="brand-title">Website Studio</h1>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <i class='bx bx-error-circle'></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-wrap">
                    <i class='bx bx-envelope input-icon-left'></i>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email') }}" required autofocus autocomplete="off" placeholder="Nhập email">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 28px;">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-wrap">
                    <i class='bx bx-lock-alt input-icon-left'></i>
                    <input type="password" id="password" name="password" class="form-control" required
                        autocomplete="new-password" placeholder="Nhập mật khẩu">
                    <button type="button" class="toggle-password-btn" id="togglePassword" aria-label="Hiện/ẩn mật khẩu">
                        <i class='bx bx-hide' id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Đăng nhập</span>
                <i class='bx bx-right-arrow-alt'></i>
            </button>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.className = isPassword ? 'bx bx-show' : 'bx bx-hide';
            });
        }
    </script>
</body>

</html>
