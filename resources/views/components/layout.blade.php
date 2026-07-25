@props(['title' => 'Road to Strong'])
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f5f5" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0a0a0a" media="(prefers-color-scheme: dark)">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" href="/icons/icon-192.png" type="image/png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Road to Strong">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-neutral-100 text-neutral-900 antialiased dark:bg-neutral-950 dark:text-neutral-50">
    <main class="mx-auto w-full max-w-md px-5 pt-12 pb-10 md:max-w-lg md:pt-16">
        {{ $slot }}
    </main>
</body>
</html>
