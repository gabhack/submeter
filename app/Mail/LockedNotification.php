<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LockedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ip;
    public $address;
    public $accesslog;

    public function __construct($ip, $address, $accesslog)
    {
        $this->ip = $ip;
        $this->address = $address;
        $this->accesslog = $accesslog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.locked_notification')
                    ->from(env('MAIL_USERNAME'), 'Plataforma Submeter 4.0')
                    ->subject("Cuenta Bloqueada por Accesos No Válidos")
                    ->bcc(env('ACCESS_CONTROL_SECURITY'), 'informatica@3seficiencia.com');
    }
}
