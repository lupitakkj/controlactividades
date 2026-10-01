<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =========================================================
           LAYOUT MÓVIL
           En celular el contenido aprovecha todo el ancho.
        ========================================================== */
        @media (max-width: 768px) {
            .mobile-content-full {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>

</head>

<body class="font-sans antialiased bg-slate-900">
    <div
        x-data="{ sidebarOpen: window.innerWidth > 768 }"
        x-init="window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) sidebarOpen = false;
            else sidebarOpen = true;
        })"
        x-cloak>

    <div class="min-h-screen bg-slate-900">

        {{-- =========================================================
             MENÚ LATERAL
        ========================================================== --}}
        @include('layouts.navigation')


        {{-- =========================================================
             CONTENIDO PRINCIPAL
        ========================================================== --}}
        <div
            class="min-h-screen min-w-0 transition-[margin] duration-300 ease-in-out"
            x-bind:class="sidebarOpen ? 'ml-64' : 'ml-16'"
            :class="{ 'mobile-content-full': window.innerWidth <= 768 }">


            {{-- =====================================================
                 HEADER DE LA PÁGINA
            ====================================================== --}}
            @isset($header)

            <header class="bg-slate-900">
                <div class="w-full px-6 lg:px-8 py-5">

                    {{ $header }}

                </div>
            </header>

            @endisset


            {{-- =====================================================
                 CONTENIDO
            ====================================================== --}}
            <main class="bg-slate-900 min-h-screen">

                {{ $slot }}

            </main>

        </div>

    </div>

    </div>

</body>

</html>