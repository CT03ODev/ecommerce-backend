<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="text-[14px] lg:text-[16px]">
    <head>
        @include('/layouts/partials/head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('/layouts/partials/scripts')
        @stack('head')

    </head>
    <body class="antialiased dark:bg-gray-900">

        <div class="min-h-screen flex flex-col justify-between">
            <!-- Page Heading -->
            <header class="sticky top-0 lg:static z-50">
                @include('/layouts/partials/navbar')
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-5">
                    {{ $slot }}
                </div>
            </main>

            <footer>
                @include('/layouts/partials/footer')
            </footer>
        </div>
        @stack('body')
    </body>
</html>
