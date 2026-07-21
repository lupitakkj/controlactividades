<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $passwordTemporal;

    public function __construct(
        $user,
        $passwordTemporal
    ) {

        $this->user = $user;

        $this->passwordTemporal =
            $passwordTemporal;
    }

    public function build()
    {
        return $this->subject(
                'Bienvenido al sistema'
            )
            ->view('emails.nuevo_usuario');
    }
}