<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LockOutNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $ip;
    public $address;
    public $date;

    public function __construct($ip, $address, $date)
    {
        $this->ip = $ip;
        $this->address = $address;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.lockout_notification')
                    ->from(env('MAIL_USERNAME'), 'Plataforma Submeter 4.0')
                    ->subject("Alerta de Acceso Incorrecto")
                    ->bcc(env('ACCESS_CONTROL_SECURITY'), 'informatica@3seficiencia.com');
    }
}
