<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rijaya</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-white overflow-hidden">

    <div class="min-h-screen flex">

        <!-- PANEL IZQUIERDO -->
        <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 relative">

            <div class="absolute inset-0 dots-glow"></div>

            <div class="flex flex-col justify-center px-24 relative z-10">

                <img src="{{ asset('img/logo.png') }}"
                    class="w-72 mb-8">

                <h1 class="text-5xl font-bold mb-6 leading-tight">
                   
                
                
                </h1>

                <p class="text-slate-300 text-xl mb-16 max-w-lg">
                    Controla actividades, diseños, avances y tiempos de
                    ejecución desde una sola plataforma.
                </p>

                <div class="grid grid-cols-3 gap-8">

                    <div class="text-center">
                        <i class="fa-regular fa-clock text-blue-400 text-4xl mb-4"></i>
                        <p class="text-slate-300">Tiempo real</p>
                    </div>

                    <div class="text-center">
                        <i class="fa-solid fa-shield-halved text-blue-400 text-4xl mb-4"></i>
                        <p class="text-slate-300">Seguridad</p>
                    </div>

                    <div class="text-center">
                        <i class="fa-solid fa-chart-column text-blue-400 text-4xl mb-4"></i>
                        <p class="text-slate-300">Reportes</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- LOGIN -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-slate-950">

            <div class="w-full max-w-lg">

                <div class="login-glow shadow-2xl">

                    <div class="login-content p-10">

                        <div class="text-center mb-10">

                            <div class="w-24 h-24 rounded-full border border-blue-500 mx-auto flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20">
                                <i class="fa-solid fa-lock text-blue-400 text-4xl"></i>
                            </div>

                            <h2 class="text-4xl font-bold">
                                Bienvenido
                                <span class="text-blue-500">
                                    de nuevo
                                </span>
                            </h2>

                            <p class="text-slate-400 mt-3">
                                Inicia sesión para continuar
                            </p>

                        </div>

                        {{ $slot }}

                    </div>

                </div>

                <div class="text-center text-slate-500 mt-8">
                    © {{ date('Y') }} Rijaya
                </div>

            </div>

        </div>

    </div>

</body>

</html>