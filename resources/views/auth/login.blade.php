<x-guest-layout>

    <x-auth-session-status class="mb-6 text-center text-green-400"
        :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label
                for="email"
                :value="__('Correo electrónico')"
                class="text-slate-300 mb-2"
            />

            <div class="relative">
                <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <x-text-input
                    id="email"
                    class="block w-full pl-12"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="ejemplo@rijaya.com"
                />
            </div>

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2 text-red-400"
            />
        </div>

        <!-- Password -->
        <div>
            <x-input-label
                for="password"
                :value="__('Contraseña')"
                class="text-slate-300 mb-2"
            />

            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <x-text-input
                    id="password"
                    class="block w-full pl-12"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
            </div>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2 text-red-400"
            />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">

            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-600 bg-slate-800 text-blue-600 focus:ring-blue-500"
                    name="remember">

                <span class="ml-2 text-sm text-slate-400">
                    Recordarme
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm text-blue-400 hover:text-blue-300 transition"
                    href="{{ route('password.request') }}"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

        </div>

        <!-- Login Button -->
        <x-primary-button>
            <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i>
            Iniciar sesión
        </x-primary-button>

    </form>

</x-guest-layout>