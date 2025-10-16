<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-Wave</title>
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="antialiased bg-gray-50">
    <div id="app"></div>
    <script>
    window.Laravel = {
        user: @json(Auth::user())
    };
    </script>

</body>
</html>
