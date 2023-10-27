<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $label_intervalo  = '';
    public $user;
    public $contador = array();
    public $tipo_count;
    public $name_contador;
    public $periodos2 = array();
    public $EAct = array();
    public $p_contratada = array();
    public $Energia_Act = array();
    public $Energia_Reac_Cap = array();
    public $Energia_Reac_Induc = array();
    public $eje = array();
    public $consumo_activa_diaria = array();
    public $consumo_capacitiva = array();
    public $consumo_inductiva = array();
    public $db_Generacion = array();
    public $eje_consu = array();
    public $consumo_activa =array();
    public $eje_analisis = array();
    public $p_demandada = array();
    public $p_contratada_analisis = array();
    public $emisiones = array();
    public $generacion = array();
    public $informes = '';
    public $consumo_GN_kWh;
    public $consumo_GN_Nm3;
    public $caudal_contratado;
    public $caudal_medio_consumido;
    public $caudal_maximo_consumido = array();
    public $presion_suministro;
    public $emisiones_gas;
    public $caudal_maximo;
    public $posicion;
    public $caudal_medio_consumido2;
    public $potencia_optima = array();

    public $historicalComparison = array();

    public function __construct($user,$contador2,$tipo_count,$periodos2,$EAct,$p_contratada,$Energia_Act,$Energia_Reac_Cap,$Energia_Reac_Induc,$eje,$consumo_activa_diaria,$consumo_capacitiva,$consumo_inductiva,$db_Generacion,$eje_consu,$consumo_activa,$eje_analisis,$p_demandada,$p_contratada_analisis,$emisiones,$generacion,$informes,$consumo_GN_kWh, $consumo_GN_Nm3,$caudal_contratado,$caudal_medio_consumido, $caudal_maximo_consumido, $presion_suministro, $emisiones_gas,$caudal_maximo,$posicion,$caudal_medio_consumido2,$potencia_optima,$label_intervalo, $historicalComparison = null)
    {
        $this->user = $user;
        $this->contador = $contador2;
        $this->tipo_count = $tipo_count;
        $this->periodos2 = $periodos2;
        $this->EAct = $EAct;
        $this->p_contratada = $p_contratada;
        $this->Energia_Act = $Energia_Act;
        $this->Energia_Reac_Cap = $Energia_Reac_Cap;
        $this->Energia_Reac_Induc = $Energia_Reac_Induc;
        $this->eje = $eje;
        $this->consumo_activa_diaria = $consumo_activa_diaria;
        $this->consumo_capacitiva = $consumo_capacitiva;
        $this->consumo_inductiva = $consumo_inductiva;
        $this->label_intervalo = $label_intervalo;


        //Analisis Potencia
        $this->eje_analisis = $eje_analisis;
        $this->p_demandada = $p_demandada;
        $this->p_contratada_analisis = $p_contratada_analisis;
        $this->potencia_optima = $potencia_optima;

        //Generación
        $this->db_Generacion = $db_Generacion;
        $this->eje_consu = $eje_consu;
        $this->consumo_activa = $consumo_activa;

        //EMISIONES
        $this->emisiones = $emisiones;

        //GENERACION PERIODICO
        $this->generacion = $generacion;

        //TIPO INFORME
        $this->informes = $informes;

        // CONTADORES DE GAS
        $this->consumo_GN_kWh = $consumo_GN_kWh;
        $this->consumo_GN_Nm3 = $consumo_GN_Nm3;
        $this->caudal_contratado = $caudal_contratado;
        $this->caudal_medio_consumido = $caudal_maximo_consumido;
        $this->caudal_maximo_consumido = $caudal_maximo_consumido;
        $this->presion_suministro = $presion_suministro;
        $this->emisiones_gas = $emisiones_gas;
        $this->caudal_maximo = $caudal_maximo;
        $this->posicion = $posicion;

        // Comparador
        $this->historicalComparison = $historicalComparison;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $str = "";
        
        $str .= 'Informe '.$this->informes;
        $str .= ' Submeter 4.0 - Contador: '.$this->contador->count_label;
        $str .= ' ('.$this->label_intervalo.')';
        
        return $this->view('emails.informes')
        ->from('submeter@submeter.email', 'Plataforma Submeter 4.0')
        ->subject($str);
        //  .$this->contador->count_label.'( '.$this->label_intervalo.' )'
    }
}
