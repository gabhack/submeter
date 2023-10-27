<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SolicitudRegistro extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var
     */

    public $nombre;
    public $apellido;
    public $empresa;
    public $correo;
    public $telefono;
    public $tipo_monitorizacion;
    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($nombre,$apellido,$empresa,$correo,$telefono,$tipo_monitorizacion)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->empresa = $empresa;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->tipo_monitorizacion = $tipo_monitorizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.solicitud_registro')
            ->from('submeter@submeter.email', 'Plataforma Submeter 4.0')
            ->subject('Solicitud de registro Submeter 4.0');
    }
}
