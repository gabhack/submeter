<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OptimizationRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $empresa;
    public $contador;
    public $cups;
    
    /**
     * Create una nueva instancia del mensaje
     * @param $empresa string Nombre o Denominacion Social
     * @param $contador string
     * @param $cups string Codigo Universal del Contador
     * @return void
     */
    public function __construct($empresa, $contador, $cups)
    {
        $this->empresa = $empresa;
        $this->contador = $contador;
        $this->cups = $cups;
    }

    /**
     * Construye el mensaje
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.plain_optimization_request')
            ->from('info@3seficiencia.com', 'Plataforma Submeter')
            ->subject('Solicitud de informe ampliado del Cálculo de Potencia Optima Eléctrica');
    }
}
