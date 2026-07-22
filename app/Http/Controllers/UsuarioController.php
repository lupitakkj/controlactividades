<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevoUsuarioMail;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class UsuarioController extends Controller
{
    public function index()
    {
        // SOLO ADMIN

        if (!auth()->user()->hasRole('Administrador')) {

            abort(403);
        }

        $usuarios = User::all();

        return view('usuarios.index', compact(
            'usuarios'
        ));
    }

    public function store(Request $request)
    {
        // SOLO ADMIN

        if (!auth()->user()->hasRole('Administrador')) {

            abort(403);
        }

        $request->validate([

            'name' => 'required',

            'email' => 'required|email|unique:users',

            'role' => 'required'
        ]);

        // GUARDAR PASSWORD TEMPORAL

        $passwordTemporal = Str::random(10);

        // CREAR USUARIO

        $user = User::create([

            'name' => $request->name,

            'email' => $request->email,

            'password' => Hash::make(
                $passwordTemporal
            )
        ]);

        // ASIGNAR ROL

        $user->assignRole(
            $request->role
        );

        // ENVIAR CORREO

        $mail = new PHPMailer(true);

        try {

            $mail->CharSet = 'UTF-8';

            $mail->isSMTP();

            $mail->Host = 'smtp.rijaya.com';

            $mail->SMTPAuth = true;

            $mail->Username = 'soporte@rijaya.com';

            $mail->Password = 'oz5V7+1.';

            $mail->Port = 587;

            $mail->SMTPSecure = false;

            $mail->SMTPAutoTLS = false;

            $mail->setFrom(
                'soporte@rijaya.com',
                'Checador'
            );

            $mail->addAddress(
                $user->email,
                $user->name
            );

            $mail->isHTML(true);

            $mail->Subject =
                'Bienvenido al sistema';

            $mail->Body = "

        <h2>Bienvenido</h2>

        <p>

            Hola {$user->name}

        </p>

        <p>

            Tu cuenta fue creada correctamente.

        </p>

        <p>

            <strong>Correo:</strong>

            {$user->email}

        </p>

        <p>

            <strong>Contraseña temporal:</strong>

            {$passwordTemporal}

        </p>

        <p>

            <a href='https://controlactividad.infinityfreeapp.com/'>

                Entrar al sistema

            </a>

        </p>
    ";

            $mail->send();
        } catch (Exception $e) {

            dd($mail->ErrorInfo);
        }

        return back()->with(
            'success',
            'Usuario creado y correo enviado'
        );
    }
}
