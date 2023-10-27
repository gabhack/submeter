<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateClienteMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var
     */
    public $codigo;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($codigo,$user)
    {
        $this->codigo = $codigo;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.envio_codigo')
            ->from('info@eficiencia.com', 'Plataforma Submeter')
            ->subject('Registro de usuario');
    }}
