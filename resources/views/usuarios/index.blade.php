<x-app-layout>
    @if(session('success'))

    <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">

        {{ session('success') }}

    </div>

    @endif
    <div class="p-6">

        <div class="flex justify-between items-center mb-8">

            <h1 class="text-3xl font-bold">
                Usuarios
            </h1>

            <a href="/dashboard"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">

                Volver

            </a>

        </div>

        <!-- FORM -->

        <div class="bg-white rounded-2xl shadow p-6 mb-8">

            <h2 class="text-xl font-bold mb-4">
                Crear usuario
            </h2>

            <form method="POST"
                action="{{ route('usuarios.store') }}">

                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="Nombre"
                        class="border rounded-lg p-3">

                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="Correo"
                        class="border rounded-lg p-3">

                    <div>

                        <label class="block mb-2 font-medium">

                            Contraseña temporal

                        </label>

                        <div class="flex gap-2">

                            <output
                                id="passwordOutput"
                                class="border rounded-lg p-3 flex-1 bg-gray-100">

                            </output>

                            <button
                                type="button"
                                onclick="generarPassword()"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded-lg">

                                Generar

                            </button>

                        </div>

                        <!-- INPUT OCULTO -->

                        <input
                            type="hidden"
                            name="password"
                            id="passwordInput">

                    </div>

                    <select
                        name="role"
                        class="border rounded-lg p-3">

                        <option value="admin">
                            Administrador
                        </option>

                        <option value="supervisor">
                            Supervisor
                        </option>

                        <option value="disenador">
                            Diseñador
                        </option>

                    </select>

                </div>

                <button
                    class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">

                    Crear usuario

                </button>

            </form>

        </div>

        <!-- TABLA -->

        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="text-left p-4">
                            Nombre
                        </th>

                        <th class="text-left p-4">
                            Correo
                        </th>

                        <th class="text-left p-4">
                            Rol
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($usuarios as $usuario)

                    <tr class="border-t">

                        <td class="p-4">

                            {{ $usuario->name }}

                        </td>

                        <td class="p-4">

                            {{ $usuario->email }}

                        </td>

                        <td class="p-4">

                            {{ $usuario->getRoleNames()->first() }}

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>
    <script>
        function generarPassword() {
            const caracteres =
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            let password = '';

            for (let i = 0; i < 10; i++) {

                password += caracteres.charAt(
                    Math.floor(
                        Math.random() *
                        caracteres.length
                    )
                );
            }

            // MOSTRAR

            document.getElementById(
                'passwordOutput'
            ).textContent = password;

            // GUARDAR EN INPUT HIDDEN

            document.getElementById(
                'passwordInput'
            ).value = password;
        }

        // GENERAR AUTOMÁTICAMENTE

        generarPassword();
    </script>
</x-app-layout>