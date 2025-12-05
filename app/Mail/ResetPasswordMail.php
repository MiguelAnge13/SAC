<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        // Construimos la URL explícitamente (sin target)
        $url = url('/password/reset/form') . '?token=' . $this->token . '&email=' . urlencode($this->user->correo);

        return $this->subject('Recuperación de contraseña - SAC')
                    ->view('emails.reset_password')
                    ->with([
                        'user' => $this->user,
                        'url' => $url,
                    ]);
    }
}

