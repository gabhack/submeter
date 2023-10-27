<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class distinctip_notification extends Mailable
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
        return $this->view('emails.distinctip_notification')
                    ->from(config('mail.mail_sender'), 'Plataforma Submeter 4.0')
                    ->subject("Acceso desde nueva dirección")
                    ->bcc(config('mail.mail_control'), 'informatica@3seficiencia.com');
    }
}
