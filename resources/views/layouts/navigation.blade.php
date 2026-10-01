<style>
    [x-cloak] {
        display: none !important;
    }
</style>
    <style>
        /* =========================================================
           MENÚ LATERAL EN CELULAR
        ========================================================== */
        @media (max-width: 768px) {
            #sidebarMobileOverlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.55);
                z-index: 40;
            }

            nav.fixed.inset-y-0.left-0 {
                z-index: 50;
            }

            /* Cuando el menú está cerrado, desaparece del área visible */
            .sidebar-mobile-hidden {
                transform: translateX(-100%) !important;
            }

            /* Botón flotante para abrir el menú */
            #btnMenuMobile {
                display: flex;
            }
        }

        @media (min-width: 769px) {
            #sidebarMobileOverlay,
            #btnMenuMobile {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            #sidebarMobileOverlay {
                display: none;
            }

            #sidebarMobileOverlay.visible {
                display: block;
            }
        }
    </style>



{{-- =========================================================
     CONTROLES DEL MENÚ EN CELULAR
========================================================== --}}
<div
    id="sidebarMobileOverlay"
    x-show="sidebarOpen && window.innerWidth <= 768"
    @click="sidebarOpen = false"
    x-transition.opacity>
</div>

<button
    id="btnMenuMobile"
    type="button"
    @click="sidebarOpen = true"
    x-show="!sidebarOpen && window.innerWidth <= 768"
    class="fixed top-4 left-4 z-[60]
           w-11 h-11 rounded-xl
           bg-slate-950 border border-slate-700
           text-white shadow-xl
           items-center justify-center">
    <i class="fa-solid fa-bars text-lg"></i>
</button>

<nav
    class="fixed inset-y-0 left-0 z-50
           bg-slate-950 border-r border-slate-800
           shadow-xl flex flex-col overflow-visible
           transition-[width] duration-300 ease-in-out"
    x-bind:class="{
        'w-64': sidebarOpen,
        'w-16': !sidebarOpen,
        'sidebar-mobile-hidden': !sidebarOpen && window.innerWidth <= 768
    }">

    {{-- =========================================================
         LOGO
    ========================================================== --}}
    <div class="h-20 flex items-center border-b border-slate-800 overflow-hidden"
        x-bind:class="sidebarOpen ? 'px-5' : 'px-3'">

        <a
            href="{{ route('dashboard') }}"
            class="flex items-center min-w-0 w-full overflow-hidden"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'">

            <x-application-logo
                class="block shrink-0 object-contain fill-current text-white transition-all duration-300"
                x-bind:class="sidebarOpen ? 'h-10 w-28' : 'h-8 w-8'" />



        </a>

    </div>


    {{-- =========================================================
         MENÚ PRINCIPAL
    ========================================================== --}}
    <div class="flex-1 overflow-y-auto px-3 py-5">

        {{-- =====================================================
             PRINCIPAL
        ====================================================== --}}
        <div class="px-3 mb-2"
            x-show="sidebarOpen"
            x-transition.opacity>

            <p class="text-[10px] font-semibold uppercase
                      tracking-[0.2em] text-slate-500">
                Principal
            </p>

        </div>


        {{-- =====================================================
             DASHBOARD
             DISEÑADOR / SUPERVISOR / ADMINISTRADOR
        ====================================================== --}}
        @if(auth()->user()->hasAnyRole([
        'Diseñador',
        'Supervisor',
        'Administrador'
        ]))

        <a
            href="{{ route('dashboard') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('dashboard')
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-chart-line w-5 text-center
                    {{ request()->routeIs('dashboard')
                        ? 'text-white'
                        : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen"
                x-transition.opacity
                class="text-sm font-medium whitespace-nowrap text-white">
                Dashboard
            </span>

        </a>

        @endif


        {{-- =====================================================
             DASHBOARD PRODUCCIÓN
             VENTAS
        ====================================================== --}}
        @if(auth()->user()->hasRole('Ventas'))

        <a
            href="{{ route('importaciones.index') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('importaciones.*')
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-file-import w-5 text-center
                    {{ request()->routeIs('importaciones.*')
                        ? 'text-white'
                        : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Importación de Pedidos
            </span>

        </a>


        <a
            href="{{ route('dashboard.produccion') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('dashboard.produccion')
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-industry w-5 text-center
                    {{ request()->routeIs('dashboard.produccion')
                        ? 'text-white'
                        : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Dashboard Producción
            </span>

        </a>

        @endif


        {{-- =====================================================
             DASHBOARD PRODUCCIÓN
             RESTO DE ROLES
        ====================================================== --}}
        @if(auth()->user()->hasAnyRole([
        'Administrador',
        'RH',
        'Compras',
        'Producción',
        'Gerente Producción',
        'Calidad',
        'Ingeniería',
        'Mantenimiento'
        ]))

        <a
            href="{{ route('dashboard.produccion') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('dashboard.produccion')
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-industry w-5 text-center
                    {{ request()->routeIs('dashboard.produccion')
                        ? 'text-white'
                        : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Dashboard Producción
            </span>

        </a>

        @endif


        {{-- =====================================================
             DASHBOARD DIRECCIÓN
             ADMINISTRADOR / GERENTE PRODUCCIÓN
        ====================================================== --}}
        @if(auth()->user()->hasAnyRole([
        'Administrador',
        'Gerente Producción'
        ]))

        <a
            href="{{ route('dashboard.direccion') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('dashboard.direccion')
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-building-columns w-5 text-center
                    {{ request()->routeIs('dashboard.direccion')
                        ? 'text-white'
                        : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Dashboard Dirección
            </span>

        </a>

        @endif


        {{-- =====================================================
             SEPARADOR
        ====================================================== --}}
        <div class="px-3 mt-7 mb-2"
            x-show="sidebarOpen"
            x-transition.opacity>

            <p class="text-[10px] font-semibold uppercase
                      tracking-[0.2em] text-slate-500">
                Operación
            </p>

        </div>


        {{-- =====================================================
             DESPIECE
        ====================================================== --}}
        <a
            href="{{ route('despiece.index') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('despiece.*')
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-screwdriver-wrench w-5 text-center
                {{ request()->routeIs('despiece.*')
                    ? 'text-white'
                    : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Despiece
            </span>

        </a>


        {{-- =====================================================
             CONTROL OPERATIVO
        ====================================================== --}}
        <a
            href="{{ route('control.operativo') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('control.operativo')
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-list-check w-5 text-center
                {{ request()->routeIs('control.operativo')
                    ? 'text-white'
                    : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Control Operativo
            </span>

        </a>


        {{-- =====================================================
             PEDIDOS TERMINADOS
        ====================================================== --}}
        <a
            href="{{ route('pedidos.terminados') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('pedidos.terminados')
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-circle-check w-5 text-center
                {{ request()->routeIs('pedidos.terminados')
                    ? 'text-white'
                    : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Pedidos Terminados
            </span>

        </a>


        {{-- =====================================================
             AGENDA CORPORATIVA
        ====================================================== --}}
        <a
            href="{{ route('agenda.index') }}"
            class="group flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'"
            {{ request()->routeIs('agenda.*')
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/30'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <i class="fa-solid fa-calendar-days w-5 text-center
                {{ request()->routeIs('agenda.*')
                    ? 'text-white'
                    : 'text-slate-500 group-hover:text-blue-400' }}">
            </i>

            <span x-show="sidebarOpen" x-transition.opacity class="text-sm font-medium whitespace-nowrap text-white">
                Agenda Corporativa
            </span>

        </a>

    </div>


    {{-- =========================================================
         USUARIO
    ========================================================== --}}
    @php

    $partes = explode(
    ' ',
    trim(Auth::user()->name)
    );

    $iniciales = strtoupper(
    substr($partes[0], 0, 1)
    );

    if (count($partes) > 1) {

    $iniciales .= strtoupper(
    substr(
    $partes[count($partes) - 1],
    0,
    1
    )
    );

    }

    $rol =
    auth()->user()
    ->getRoleNames()
    ->first()
    ?? 'Usuario';

    @endphp


    <div
        class="border-t border-slate-800 p-3 relative"
        x-data="{ userMenuOpen: false }"
        @click.outside="userMenuOpen = false">

        <button
            type="button"
            @click="userMenuOpen = !userMenuOpen"
            class="w-full flex items-center
                   px-2 py-2.5 rounded-xl
                   hover:bg-slate-800
                   transition-colors duration-200"
            x-bind:class="sidebarOpen ? 'gap-3' : 'justify-center'">

            <div
                class="w-10 h-10 shrink-0 rounded-full
                       bg-gradient-to-br from-blue-600 to-indigo-600
                       flex items-center justify-center
                       text-white font-bold text-sm shadow-md">
                {{ $iniciales }}
            </div>

            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="flex-1 min-w-0 text-left">

                <div class="text-xs uppercase tracking-widest text-slate-500">
                    {{ $rol }}
                </div>

                <div class="text-sm font-medium text-white truncate">
                    {{ Auth::user()->name }}
                </div>

            </div>

            <svg
                x-show="sidebarOpen"
                x-transition.opacity
                class="w-4 h-4 text-slate-500 shrink-0 transition-transform duration-200"
                x-bind:class="userMenuOpen ? 'rotate-180' : ''"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">

                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />

            </svg>

        </button>

        <div
            x-show="userMenuOpen"
            x-transition
            class="absolute bottom-full right-3 mb-2 w-56
                   bg-white rounded-xl shadow-2xl
                   border border-slate-200
                   overflow-hidden z-[100]"
            style="display: none;">

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3
                       px-4 py-3
                       text-sm text-slate-700
                       hover:bg-slate-100
                       transition">

                <i class="fa-solid fa-user w-4 text-center text-slate-500"></i>
                <span>Perfil</span>

            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full flex items-center gap-3
                           px-4 py-3
                           text-sm text-red-600
                           hover:bg-red-50
                           transition
                           text-left">

                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                    <span>Cerrar sesión</span>

                </button>
            </form>

        </div>

    </div>


    {{-- =========================================================
         BOTÓN COLAPSAR / EXPANDIR
    ========================================================== --}}
    <div
        class="absolute top-5 -right-3 z-10"
        x-show="window.innerWidth > 768">

        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="w-7 h-7 rounded-full
                   bg-slate-800 border border-slate-700
                   text-slate-300 hover:text-white
                   hover:bg-blue-600
                   flex items-center justify-center
                   shadow-lg transition-all duration-200"
            x-bind:title="sidebarOpen ? 'Contraer menú' : 'Expandir menú'">

            <svg
                class="w-4 h-4 transition-transform duration-300"
                x-bind:class="sidebarOpen ? 'rotate-180' : ''"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7" />

            </svg>

        </button>

    </div>

</nav>