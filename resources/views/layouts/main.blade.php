@yield('misc_top')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title')</title>

    @fonts

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif

    @yield('head_links')
</head>

<body
    class="min-h-screen bg-linear-to-br from-white via-lime-50 from-10% via-20% to-green-100 font-sans text-slate-800 antialiased">
    @yield('content')
</body>

</html>
