<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title inertia>Admin Dashboard | Norm</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <script>
        window.__STUDIO_USER__ = "{{ Auth::user()->name ?? 'Admin' }}";
    </script>

    @vite(['resources/js/admin/main.js'])
    @inertiaHead
</head>
<body style="margin: 0; padding: 0; overflow: hidden; background: #f0f2f5;">
    @inertia
</body>
</html>

