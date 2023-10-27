<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Alertas;
use App\Mail\AlertaPotenciaConsumo;
use App\Mail\AlertaPotenciaGeneracion;
use App\Mail\AlertaPotenciaGas;
use App\Mail\AlertaPotenciaConsumoReactiva;
use App\Mail\AlertaPotenciaCaudalGas;
use Illuminate\Support\Facades\Mail;
use App\Count;
use App\User;
use Session;
use Auth;

class AlertPotenciaConsumo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerta_potencia:consumo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alertas por Potencia en contadores de consumo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $type_alert = Alertas::where('alert_type',1)->get()->toArray();        
        
        $fecha = \Carbon\Carbon::yesterday()->toDateString();
        $date_antier = \Carbon\Carbon::now()->subDays(2)->toDateString();
        $date_from = $fecha;
        $date_to = $fecha;        

        $aux = array();
        $i = 0;
        $h = 0;
        
        foreach ($type_alert as $alert) 
        {
            $db_EAct = array();
            $db_p_contratada = array();
            $contadorObj = Count::where('count_label', $alert['contador'])->first();
            $dataMail = [];
            
            $alert["emails"] = preg_replace('/\s+/S', "", $alert["emails"]);;
            if($alert['alert_type'] == 1)
            {
                if($contadorObj && $contadorObj->tipo == 1 && $contadorObj->tarifa == 1)
                {
                    $user = User::where('id',$alert['user_id'])->first();
                    $contador = $alert['contador'];
                    config(['database.connections.mysql2.host' => $contadorObj->host]);
                    config(['database.connections.mysql2.port' => $contadorObj->port]);
                    config(['database.connections.mysql2.database' => $contadorObj->database]);
                    config(['database.connections.mysql2.username' => $contadorObj->username]);
                    config(['database.connections.mysql2.password' => $contadorObj->password]);
                    $db = \DB::connection('mysql2');

                    $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;

                    $total = 0;

                    $db_p_contratada = $db->table('Potencia_Contratada')->select(\DB::raw("Potencia_contratada p_contratada"))->groupBy('Periodo')->get()->toArray();                    
                    $db_EAct = $db->table('Analisis_Potencia')->select(\DB::raw("MAX(`Potencia Demandada (kW)`) as prom, Periodo, RIGHT(Periodo,1) AS periodo_int"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get();

                    $periodos = [];
                    $hora = [];
                    $poten = [];
                    
                    $porcentajes = [1 => '110', 2 => '115', 3 => '120', 4 => '105'];
                    
                    foreach ($db_EAct as $value) {

                        $aux_hora = $db->table('Analisis_Potencia')->select("time")->where(\DB::raw("`Potencia Demandada (kW)`"),$value->prom)->where('date','>=',$date_from)->where('date','<=',$date_to)->first();
                        $periodo = $value->periodo_int - 1;                       
                        
                        if($alert['alert_value'] == 1 && isset($value) && count($db_p_contratada) > $periodo)
                        {                                
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.1)
                            {     
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }
                        elseif($alert['alert_value'] == 2 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.15)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }
                        elseif($alert['alert_value'] == 3 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.2)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        } 
                        elseif($alert['alert_value'] == 4 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.05)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }                                
                        }
                    }
                    
                    $dataMail["contador"] = $contadorObj;
                    $dataMail["porcentajes"] = $porcentajes;
                    $dataMail["horas"] = $hora;
                    $dataMail["periodos"] = $periodos;
                    $dataMail["potencias"] = $poten;
                    $dataMail["CUPS"] = $CUPS;
                    $dataMail["user"] = $user;
                    $dataMail["fecha"] = $fecha;
                    $dataMail["alert"] = $alert;
                    
                    $mails = explode(';', $alert['emails']);                                          
                    foreach ($mails as $mail) 
                    {
                        if(count($periodos) > 0 && $alert['alert_type'] == 1)
                        {
                            
                            Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaConsumo($dataMail));
                            sleep(3);
                        }
                    }

                    \DB::disconnect('mysql2');
                }
                else if($contadorObj && $contadorObj->tipo == 2 && $contadorObj->tarifa == 1)
                {
                    $user = User::where('id', $alert['user_id'])->first();                                        

                    config(['database.connections.mysql2.host' => $contadorObj->host]);
                    config(['database.connections.mysql2.port' => $contadorObj->port]);
                    config(['database.connections.mysql2.database' => $contadorObj->database]);
                    config(['database.connections.mysql2.username' => $contadorObj->username]);
                    config(['database.connections.mysql2.password' => $contadorObj->password]);
                    $db = \DB::connection('mysql2');

                    $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;

                    $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("SUM(`Generacion Energia (kWh)`) generacion_energia, time"))->where('date',$date_from)->orderBy('generacion_energia','DESC')->first();
                    $balance_antier = $db->table('Balance_Neto_Diario')->select(\DB::raw("SUM(`Generacion Energia (kWh)`) generacion_energia, DATE_FORMAT(time, '%H:%i') time"))->where('date',$date_antier)->first();
                    
                    $porcentajes_alerta = [4=>0, 5=>0.05, 6=>0.1, 7=>0.2, 8=>0.25, 9=>0.3, 10=>0.4, 1 => 0.5,
                                             2 => 0.7, 3=>0.9];
                    $porcentajes_alerta_s = [4=>'0', 5=>'5%', 6=>'10%', 7=>'20%', 8=>'25%', 9=>'30%', 10=>'40%', 1 => '50%', 
                                            2 => '70%', 3=>'90%'];
                    $search_types_s = [1 => '>=', 2 => '<=', 3 => '='];
                    $search_types_html = [1 => '&gt;&#61;', 2 => '&lt;&#61;', 3 => '&#61;'];
                    
                    $balance_actual = abs($balance->generacion_energia);
                    $balance_anterior = abs($balance_antier->generacion_energia);                    
                    
                    $send = false;
                    if($alert['alert_value'] == 4)
                    {
                        if($alert['search_type'] == 1 && $balance_actual >= 0.0)
                        {
                            $send = true;
                        }
                        elseif($alert['search_type'] == 2 && $balance_actual <= 0.0)
                        {
                            $send = true;
                        }
                        elseif($alert['search_type'] == 3 && $balance_actual == 0.0)
                        {
                            $send = true;
                        }
                    }
                    elseif(array_key_exists($alert['alert_value'], $porcentajes_alerta) && array_key_exists($alert['search_type']
                            , $search_types_s))
                    {
                        if($alert['search_type'] == 1 && $balance_actual >= $balance_anterior*(1 + $porcentajes_alerta[$alert['alert_value']]))
                        {
                            $send = true;
                        }
                        elseif($alert['search_type'] == 2 && $balance_actual <= $balance_anterior*(1 - $porcentajes_alerta[$alert['alert_value']]))
                        {
                            $send = true;
                        }
                        elseif($alert['search_type'] == 3 && $balance_actual == $balance_anterior*$porcentajes_alerta[$alert['alert_value']])
                        {
                            $send = true;
                        }
                    }                    
                    
                    $dataMail["user"] = $user;
                    $dataMail["CUPS"] = $CUPS;
                    $dataMail["contador"] = $contadorObj;
                    $dataMail["porcentajes_alerta_s"] = $porcentajes_alerta_s;
                    $dataMail["search_types_html"] = $search_types_html;
                    $dataMail["balance_actual"] = abs($balance_actual);
                    $dataMail["balance_anterior"] = abs($balance_anterior);
                    $dataMail["date_actual"] = $date_from;
                    $dataMail["date_anterior"] = $date_antier;
                    $dataMail["alert"] = $alert;
                    
                    $mails = explode(';', $alert['emails']);                                          
                    foreach ($mails as $key => $mail) 
                    {
                        if($send && $alert['alert_type'] == 1)
                        {
                            Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaGeneracion($dataMail));
                            sleep(3);
                        }
                    }
                    \DB::disconnect('mysql2');                    
                }
                else if($contadorObj && $contadorObj->tipo == 3 && $contadorObj->tarifa == 1)
                {
                    $user = User::where('id',$alert['user_id'])->first();
                    $contador = $alert['contador'];
                    $h = 0;
                    $periodos = 'xx';
                    $hora = '';
                    $poten = '';
                    config(['database.connections.mysql2.host' => $contadorObj->host]);
                    config(['database.connections.mysql2.port' => $contadorObj->port]);
                    config(['database.connections.mysql2.database' => $contadorObj->database]);
                    config(['database.connections.mysql2.username' => $contadorObj->username]);
                    config(['database.connections.mysql2.password' => $contadorObj->password]);
                    $db = \DB::connection('mysql2');
                        
                        $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;

                        $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("MAX(`Consumo GN (Nm3)`) consumo, DATE_FORMAT(time, '%H:%i') time"))->where('date',$date_from)->first();
                        $consumo_GN_Nm3_2 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("MAX(`Consumo GN (Nm3)`) consumo, DATE_FORMAT(time, '%H:%i') time"))->where('date',$date_antier)->first();

                        if($alert['alert_value'] == 1 && !is_null($consumo_GN_Nm3) && !is_null($consumo_GN_Nm3->consumo))
                        {
                            if($consumo_GN_Nm3->consumo >= $consumo_GN_Nm3_2->consumo*1.1)
                            {
                                $periodos = '>=10%';
                                $hora = $consumo_GN_Nm3->time;
                                $poten = number_format($consumo_GN_Nm3->consumo,3,',','.');
                            }

                        }elseif($alert['alert_value'] == 2 && !is_null($consumo_GN_Nm3) && !is_null($consumo_GN_Nm3->consumo)){
                            if($consumo_GN_Nm3->consumo >= $consumo_GN_Nm3_2->consumo*1.15)
                            {
                                $periodos = '>=15%';
                                $hora = $consumo_GN_Nm3->time;
                                $poten = number_format($consumo_GN_Nm3->consumo,3,',','.');
                            }
                        }elseif ($alert['alert_value'] == 3 && !is_null($consumo_GN_Nm3) && !is_null($consumo_GN_Nm3->consumo)) {
                            if($consumo_GN_Nm3->consumo >= $consumo_GN_Nm3_2->consumo*1.2)
                            {
                                $periodos = '>=20%';
                                $hora = $consumo_GN_Nm3->time;
                                $poten = number_format($consumo_GN_Nm3->consumo,3,',','.');
                            }
                        }
                        $mails = explode(';', $alert['emails']);                                          
                        foreach ($mails as $key => $mail) {
                            if($periodos != 'xx' && $alert['alert_type'] == 1)
                            {
                                Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaGas($user,$contador,$fecha,$alert,$periodos,$date_antier,$hora,$poten,$CUPS, $dataMail));
                                sleep(3);
                            }
                        }

                    \DB::disconnect('mysql2');
                }
                else if($contadorObj && $contadorObj->tipo == 1 && $contadorObj->tarifa != 1)
                {
                    $k = 0;
                    $user = User::where('id',$alert['user_id'])->first();
                    $contador = $alert['contador'];                    
                    config(['database.connections.mysql2.host' => $contadorObj->host]);
                    config(['database.connections.mysql2.port' => $contadorObj->port]);
                    config(['database.connections.mysql2.database' => $contadorObj->database]);
                    config(['database.connections.mysql2.username' => $contadorObj->username]);
                    config(['database.connections.mysql2.password' => $contadorObj->password]);
                    $db = \DB::connection('mysql2');

                    $porcentajes = [1 => '110', 2 => '115', 3 => '120', 4 => '105'];
                    $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;

                    $periodos = [];
                    $hora = [];
                    $poten = [];                    

                    $db_p_contratada = $db->table('Potencia_Contratada')->select(\DB::raw("Potencia_contratada p_contratada"))->groupBy('Periodo')->get()->toArray();
                    $db_EAct = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("MAX(`Potencia Demandada (kW)`) as prom, Periodo, RIGHT(Periodo,1) AS periodo_int"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get();

                    foreach ($db_EAct as $value) 
                    {
                        $aux_hora = $db->table('Analisis_Potencia')->select("time")->where(\DB::raw("`Potencia Demandada (kW)`"),$value->prom)->where('date','>=',$date_from)->where('date','<=',$date_to)->first();
                        $periodo = $value->periodo_int - 1;
                        
                        if($alert['alert_value'] == 1 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.1)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }
                        elseif($alert['alert_value'] == 2 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.15)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }
                        elseif($alert['alert_value'] == 3 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.2)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }
                        elseif($alert['alert_value'] == 4 && isset($value) && count($db_p_contratada) > $periodo)
                        {
                            if($value->prom >= ($db_p_contratada[$periodo]->p_contratada)*1.05)
                            {
                                $periodos[] = $value->Periodo;
                                $hora[] = $aux_hora->time;
                                $poten[] = number_format($value->prom,0,',','.');
                            }
                        }                        
                    }

                    $dataMail["user"] = $user;
                    $dataMail["contador"] = $contadorObj;
                    $dataMail["porcentajes"] = $porcentajes;
                    $dataMail["horas"] = $hora;
                    $dataMail["periodos"] = $periodos;
                    $dataMail["potencias"] = $poten;
                    $dataMail["CUPS"] = $CUPS;
                    $dataMail["user"] = $user;
                    $dataMail["fecha"] = $fecha;
                    $dataMail["alert"] = $alert;
                    
                    $mails = explode(';', $alert['emails']);
                    foreach ($mails as $mail)
                    {
                        if(count($periodos) > 0 && $alert['alert_type'] == 1)
                        {
                            
                            Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaConsumo($dataMail));
                            sleep(3);
                        }
                    }

                    \DB::disconnect('mysql2');

                }
            }
        }
        // 
        
        $type_alert = Alertas::where('alert_type',2)->get()->toArray();        
        foreach ($type_alert as $alert) 
        {
            $dataMail = [];
            $contadorObj = Count::where('count_label', $alert['contador'])->first();  
            
            $alert["emails"] = preg_replace('/\s+/S', "", $alert["emails"]);;
            if($contadorObj && $contadorObj->tipo == 1 && $alert['alert_type'] == 2)
            {
                $user = User::where('id',$alert['user_id'])->first();
                
                config(['database.connections.mysql2.host' => $contadorObj->host]);
                config(['database.connections.mysql2.port' => $contadorObj->port]);
                config(['database.connections.mysql2.database' => $contadorObj->database]);
                config(['database.connections.mysql2.username' => $contadorObj->username]);
                config(['database.connections.mysql2.password' => $contadorObj->password]);
                $db = \DB::connection('mysql2');
                $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;
                $dataReactiva = $db->table('ZPI_Precio_Energia_Reactiva_Diario')
                                    ->select(\DB::raw("`Coseno_Fi` fase, Periodo as periodo, RIGHT(Periodo,1) AS periodo_int"))
                                    ->where('date','>=',$date_from)->where('date','<=',$date_to)->orderBy("Periodo")->get();

                $send = false;
                $dataValores = [1 => "0.95", 2 => "0.90", 3 => "0.85"];
                $periodos = [];
                $energia = [];
                if($dataReactiva)
                {
                    foreach($dataReactiva as $reactiva)
                    {
                        if($reactiva->fase != 0)
                        {
                            $cosfi = $reactiva->fase;
                        }
                        else
                        {
                            $cosfi = 1;
                        }
    
                        if($alert['alert_value'] == 1 && $cosfi <= 0.95)
                        {
                            $periodos[] = $reactiva->periodo;
                            $energia[] = number_format($cosfi,3,',','.');
                            $send = true;                                             
                        }
                        elseif($alert['alert_value'] == 2 && $cosfi <= 0.90)
                        {
                            $periodos[] = $reactiva->periodo;
                            $energia[] = number_format($cosfi,3,',','.');
                            $send = true;
                        }
                        elseif($cosfi <= 0.85 && $alert['alert_value'] == 3)
                        {
                            $periodos[] = $reactiva->periodo;
                            $energia[] = number_format($cosfi,3,',','.');
                            $send = true;
                        }
                    }
                }
                
                $dataMail["CUPS"] = $CUPS;
                $dataMail["user"] = $user;
                $dataMail["alert"] = $alert;
                $dataMail["contador"] = $contadorObj;
                $dataMail["fecha"] = $fecha;
                $dataMail["dataValores"] = $dataValores;
                $dataMail["periodos"] = $periodos;
                $dataMail["energia"] = $energia;

                $mails = explode(';', $alert['emails']);
                
                foreach ($mails as $key => $mail) 
                {
                    if($send && $alert['alert_type'] == 2)
                    {
                        Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaConsumoReactiva($dataMail));
                    }
                    sleep(3);
                }
                                                        
                \DB::disconnect('mysql2');
                
            }
            else if($contadorObj && $contadorObj->tipo == 3 && $contadorObj->tarifa == 1)
            {
                $user = User::where('id',$alert['user_id'])->first();                    
                
                config(['database.connections.mysql2.host' => $contadorObj->host]);
                config(['database.connections.mysql2.port' => $contadorObj->port]);
                config(['database.connections.mysql2.database' => $contadorObj->database]);
                config(['database.connections.mysql2.username' => $contadorObj->username]);
                config(['database.connections.mysql2.password' => $contadorObj->password]);
                $db = \DB::connection('mysql2');
                $CUPS = $db->table('Area_Cliente')->select(\DB::raw('CUPS'))->first()->CUPS;

                $QD_contratado = $db->table('Caudal_diario_contratado')->select(\DB::raw("`Caudal_diario_contratado` QD"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

                $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(`Consumo GN (kWh)`) consumo, DATE_FORMAT(time, '%H:%i') time"))->where('date',$date_from)->orderBy('consumo','DESC')->first();

                $dataValores = [1 => "10", 2 => "15", 3 => "20"];
                
                if(isset($QD_contratado->QD))
                {
                    if($alert['alert_value'] == 1 && !is_null($consumo_GN_kWh) && !is_null($QD_contratado) && $consumo_GN_kWh->consumo >= $QD_contratado->QD*1.1)
                    {
                        $hora = $consumo_GN_kWh->time;
                        $potencia = number_format($consumo_GN_kWh->consumo,3,',','.');

                    }elseif($alert['alert_value'] == 2 && !is_null($consumo_GN_kWh) && !is_null($QD_contratado) && $consumo_GN_kWh->consumo >= $QD_contratado->QD*1.15){

                        $hora = $consumo_GN_kWh->time;
                        $potencia = number_format($consumo_GN_kWh->consumo,3,',','.');
                        
                    }elseif($alert['alert_value'] == 3 && !is_null($consumo_GN_kWh) && !is_null($QD_contratado) && $consumo_GN_kWh->consumo >= $QD_contratado->QD*1.2)
                    {
                        $hora = $consumo_GN_kWh->time;
                        $potencia = number_format($consumo_GN_kWh->consumo,3,',','.');
                    }

                }
                
                $dataMail["CUPS"] = $CUPS;
                $dataMail["user"] = $user;
                $dataMail["alert"] = $alert;
                $dataMail["contador"] = $contadorObj;
                $dataMail["fecha"] = $fecha;
                $dataMail["hora"] = $fecha;
                $dataMail["dataValores"] = $dataValores;
                $dataMail["potencia"] = $potencia;
                
                $mails = explode(';', $alert['emails']);                                          
                foreach ($mails as $key => $mail) 
                {
                    if($periodos != '' && $alert['alert_type'] == 2)
                    {
                        Mail::to($mail,'Submeter 4.0 (Alerta de exceso de Potencia Demandada)')->send(new AlertaPotenciaCaudalGas($dataMail));
                    }
                    sleep(3);
                }

                \DB::disconnect('mysql2');                
            }
        }


    }
}
