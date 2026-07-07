<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark);"
    :class="{ 'dark': dark }"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name', 'DigiStore') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Platform jual beli produk digital terpercaya' }}" />

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

    {{ $slot }}

    @livewireScripts
</body>
</html>
