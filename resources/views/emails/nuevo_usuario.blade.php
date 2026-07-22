<h2>

    Bienvenido al sistema

</h2>

<p>

    Hola {{ $user->name }},

</p>

<p>

    Tu cuenta fue creada correctamente.

</p>

<p>

    <strong>Correo:</strong>

    {{ $user->email }}

</p>

<p>

    <strong>Contraseña temporal:</strong>

    {{ $passwordTemporal }}

</p>

<p>

    <strong>Acceso:</strong>

    <a href="https://controlactividad.infinityfreeapp.com/">

        Entrar al sistema

    </a>

</p>