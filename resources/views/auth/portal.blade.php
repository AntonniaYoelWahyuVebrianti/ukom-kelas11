<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'register' ? 'Register' : 'Login' }} | K-Wave</title>
    @vite(['resources/css/app.css', 'resources/js/auth.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="antialiased">
    <div id="auth-app" data-initial-mode="{{ $mode === 'register' ? 'register' : 'login' }}"></div>
    <script>
        window.Laravel = {
            user: @json(Auth::user())
        };
    </script>
</body>
</html>
