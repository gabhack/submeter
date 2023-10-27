<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Count;
use App\User;
use Auth;
use Session;

class ResumenEnergiaController extends Controller
{
    function ResumenEnergiaPotencia($id,Request $request)
    {
        $contador = strtolower(request()->input('contador'));
        $user = User::find($id);

        $eje = array();
        $consumo_activa = array();
        $max_consumo_activa = 0;
        $consumo_capacitiva = array();
        $consumo_inductiva = array();
        $potencia_optima = array();
        $generacion = array();
        $balance2 = array();

        $interval = "";
        $flash_current_count = null;
        $session = Session::get('_flash');
        if(array_key_exists('intervalos', $session))
        {
            $interval = $session['intervalos'];
            if(array_key_exists("current_count", $session))
            {
                $flash_current_count = $session['current_count'];
            }
        }

        $dataRequest = [];
        $dataRequest["user"] = $user;
        $dataRequest["contador"] = $contador;
        $dataRequest["interval"] = $interval;
        $dataRequest["flash_current_count"] = $flash_current_count;

        $contador2 = ContadorController::getCurrrentController($dataRequest);


        $tipo_count      = $contador2->tipo;
		$subtipo_count   = $contador2->subtipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST', $contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);

        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            dd("La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
        }

        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        $flash = Session::get('_flash');

        if(array_key_exists("date_from_personalice", $flash)){
            $date_from = $flash['date_from_personalice'];
        }

        if(!isset($date_from)){
            $dateInfo = $this->getDatesAnalysis();
            $date_from = $dateInfo["date_from"];
            $date_to = $dateInfo["date_to"];
            $label_intervalo = $dateInfo["date_label"];
        } else {
            $flash = Session::get('_flash');

            $date_to = Session::get('_flash')['date_to_personalice'];
            if(array_key_exists("label_intervalo_navigation", $flash)){
                $dateInfo = $this->getDatesAnalysis();
                $label_intervalo = $dateInfo["date_label"];
            } else {
                $dateInfo = $this->getDatesAnalysis();
                $label_intervalo = $dateInfo["date_label"];
            }
        }

        $user = Auth::user();//usuario logeado
        $titulo = 'Energía y Potencia';

        $dataPeriodo = [];
        $dataPeriodo['interval'] = $interval;
        $dataPeriodo['date_from'] = $date_from;
        $dataPeriodo['date_to'] = $date_to;
        $dataSubperiodo = $this->getInfoSubPeriodo($dataPeriodo);


        // SE OBTIENEN LOS PERÍODOS DEL MES DE ACUERDO A LA TARIFA

        $periodos2 = array();
        $db_EAct = array();
        $EAct = array();
        $db_p_contratada = array();
        $p_contratada = array();
        $periodos_coste = array();

        // SE CREAN ARRAYS CON LOS PERÍODOS DISPONIBLES EN LA COMPAÑÍA
        if($tipo_count < 3)
            $aux_periodos = $db->table('Potencia_Contratada')->select(\DB::raw("COUNT(*) cont"))->groupBy('Periodo')->get()->toArray();
        else
            $aux_periodos = array();


        // dd(count($aux_periodos));
        for ($i=1; $i <= count($aux_periodos) ; $i++) {
            $periodos2[] = 'P'.$i;
            $periodos_coste[] = 'P'.$i;
        }
        // SE UNE AL ARRAY DE PERÍODOS DE LA COMPAÑÍA, LA OPCIÓN DE TOTAL
        array_push($periodos_coste, "Total");
        // dd($periodos_coste,$periodos2);

        $MES = $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();
        $total = 0;
        if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        {
            // dd($contador2->tarifa);
            if(!empty($MES))
            {
                // dd($contador2);
                $k = 0;
                foreach ($MES as $mes) {
                    foreach ($periodos2 as $p) {
                        // SELECCIONA LA ENERGÍA ACTIVA MÁXIMA CONSUMIDA EN EL PERÍODO SELECCIONADO
                        if($tipo_count == 1)
                        {
                            // if($contador2->database == 'Contador_3.0A')
                            // {
                            //     $db_EAct[] = $db->table('ZPI_Contador_Periodos')->select(\DB::raw("MAX(`EAct imp(kWh)`)*4 as prom, Periodo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                            // }elseif($contador2->database == 'Contador_3.1A'){
                            //     $db_EAct[] = $db->table('Datos_Contador')->select(\DB::raw("MAX(`EAct imp(kWh)`)*4 as prom"))->join('Tarifa',"Datos_Contador.time",">=",\DB::raw("Tarifa.hora_start AND Tarifa.Mes = ".$mes->MES." AND Datos_Contador.time < Tarifa.hora_end"))->where("Datos_Contador.date", '>=',$date_from)->where("Datos_Contador.date", '<=',$date_to)->where("Tarifa.Periodo",$p)->where(\DB::raw('MONTH(Datos_Contador.date)'),$mes->MES)->get()->toArray();
                            // }else{
                                $db_EAct[] = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("MAX(`Potencia Demandada (kW)`) as prom"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
                                break;
                            // }
                            if(is_null($db_EAct[$k][0]->prom))
                            {
                                $db_EAct[$k][0]->prom = 0;
                            }

                        }
                        $k++;
                    }
                }
            }
        }else{
            $db_EAct[] = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("MAX(`Potencia Demandada (kW)`) as prom"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
            // dd($db_EAct);
        }
        if($tipo_count < 3)
        {
            $index = 0;
            if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
            {
                $db_coste_potencia = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                foreach ($db_coste_potencia as $coste_poten) {
                    $aux_index = 'costeP';
                    $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->costeP1;
                    $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->costeP2;
                    $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->costeP3;
                    $aux_coste_potencia[$index][$aux_index.($index+4)] = $coste_poten->costeP4;
                    $aux_coste_potencia[$index][$aux_index.($index+5)] = $coste_poten->costeP5;
                    $aux_coste_potencia[$index][$aux_index.($index+6)] = $coste_poten->costeP6;
                    $index++;
                }
            }else{
                // $db_coste_potencia = $db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where(\DB::raw('MONTH(`Max(``date``)`)'),'>=',\Carbon\Carbon::parse($date_to)->month)->get()->toArray();
                $db_coste_potencia = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'costeP1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'costeP2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'costeP3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                foreach ($db_coste_potencia as $coste_poten) {
                    $aux_index = 'costeP';
                    $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->costeP1*1;
                    $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->costeP2*1;
                    $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->costeP3*1;
                    $index++;
                }
                // dd($db_coste_potencia, $aux_coste_reactiva_potencia);
            }
            //$db_excesos[] = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(`Coste Exceso Potencia (€)`) AS coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
            //$db_coste_termino_energia[] = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(`Coste Termino Energia (€)`) as cost"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
            
            // Para obtener los coeficientes de exceso de la base de datos
            $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
            $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
            $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
            $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
            $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
            $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
            $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();

            $index = 0;
            if($contador2->database == 'Prueba_Contador_6.0_V3' && ($contador2->tarifa != 2 && $contador2->tarifa != 3))
            {
                $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

                $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                foreach ($db_excesos as $excesos) {
                    $aux_index = 'costeP';
                    $aux_excesos[$index][$aux_index.($index+1)] = $excesos->costeP1;
                    $aux_excesos[$index][$aux_index.($index+2)] = $excesos->costeP2;
                    $aux_excesos[$index][$aux_index.($index+3)] = $excesos->costeP3;
                    $aux_excesos[$index][$aux_index.($index+4)] = $excesos->costeP4;
                    $aux_excesos[$index][$aux_index.($index+5)] = $excesos->costeP5;
                    $aux_excesos[$index][$aux_index.($index+6)] = $excesos->costeP6;
                    $index++;
                }

                // dd($db_excesos2, $db_excesos);
            }elseif(($contador2->tarifa != 2 && $contador2->tarifa != 3)){
                // $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                // $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                // Se anexa validacion de periodo para que se pueda considerar como mes aquellos peridos mayores al mes.
                if($interval < 7){
                    $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                }
                else{
                    $db_excesos = $db->select(\DB::raw("SELECT $ktep->valor_coeficiente * $kiP1->valor_coeficiente * SUM(CASE WHEN Periodo = 'P1' THEN `Exceso` ELSE 0 END) as costeP1,$ktep->valor_coeficiente * $kiP2->valor_coeficiente * SUM(CASE WHEN Periodo = 'P2' THEN `Exceso` ELSE 0 END) as costeP2,$ktep->valor_coeficiente * $kiP3->valor_coeficiente * SUM(CASE WHEN Periodo = 'P3' THEN `Exceso` ELSE 0 END) as costeP3,$ktep->valor_coeficiente * $kiP4->valor_coeficiente * SUM(CASE WHEN Periodo = 'P4' THEN `Exceso` ELSE 0 END) as costeP4,$ktep->valor_coeficiente * $kiP5->valor_coeficiente * SUM(CASE WHEN Periodo = 'P5' THEN `Exceso` ELSE 0 END) as costeP5,$ktep->valor_coeficiente * $kiP6->valor_coeficiente * SUM(CASE WHEN Periodo = 'P6' THEN `Exceso` ELSE 0 END) as costeP6 FROM (SELECT MONTH(`date`) bloque,Periodo,SQRT(SUM(`Exceso De Potencia (kW)`)) as Exceso FROM ZPI_Dias_Excesos_y_Precio_Contratada WHERE `date` BETWEEN '$date_from' AND '$date_to' GROUP BY MONTH(`date`),Periodo) z"));
                }


                foreach ($db_excesos as $excesos) {
                    $aux_index = 'costeP';
                    $aux_excesos[$index][$aux_index.($index+1)] = $excesos->costeP1;
                    $aux_excesos[$index][$aux_index.($index+2)] = $excesos->costeP2;
                    $aux_excesos[$index][$aux_index.($index+3)] = $excesos->costeP3;
                    $aux_excesos[$index][$aux_index.($index+4)] = $excesos->costeP4;
                    $aux_excesos[$index][$aux_index.($index+5)] = $excesos->costeP5;
                    $aux_excesos[$index][$aux_index.($index+6)] = $excesos->costeP6;
                    $index++;
                }

                // $db_excesos2 = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            }
            $index = 0;
             if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
            {
                $db_coste_termino_energia = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                foreach ($db_coste_termino_energia as $energia)
                {
                    $aux_index = 'costeP';
                    $aux_energia[$index][$aux_index.($index+1)] = $energia->costeP1;
                    $aux_energia[$index][$aux_index.($index+2)] = $energia->costeP2;
                    $aux_energia[$index][$aux_index.($index+3)] = $energia->costeP3;
                    $aux_energia[$index][$aux_index.($index+4)] = $energia->costeP4;
                    $aux_energia[$index][$aux_index.($index+5)] = $energia->costeP5;
                    $aux_energia[$index][$aux_index.($index+6)] = $energia->costeP6;
                    $index++;
                }
                // dd($db_coste_termino_energia, $aux_energia);
            }else{

                $db_coste_termino_energia = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                foreach ($db_coste_termino_energia as $energia)
                {
                    $aux_index = 'costeP';
                    $aux_energia[$index][$aux_index.($index+1)] = $energia->costeP1;
                    $aux_energia[$index][$aux_index.($index+2)] = $energia->costeP2;
                    $aux_energia[$index][$aux_index.($index+3)] = $energia->costeP3;
                    $index++;
                }
            }

            $index = 0;


            //dd($db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get());
            //dd($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first());

            if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first())
            {
                $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get();
            }else{
                $potencia_optima[0]['p_optima'] =0;
                $potencia_optima[1]['p_optima'] =0;
                $potencia_optima[2]['p_optima'] =0;
                $potencia_optima[3]['p_optima'] =0;
                $potencia_optima[4]['p_optima'] =0;
                $potencia_optima[5]['p_optima'] =0;
            }
            // dd($potencia_optima);


            $db_p_contratada[] = $db->table('Potencia_Contratada')->select(\DB::raw("Potencia_contratada p_contratada"))->groupBy('Periodo')->get()->toArray();

            // INICIALIZA EL VECTOR DONDE SE ALMACENARÁ EL COSTO DE POTENCIA
            // DE ACUERDO AL INTERVALO SELECCIONADO
            $flag_aux = 0;
            for ($i=0; $i < count($aux_periodos); $i++) {
                $coste_potencia[] = 0;
                $coste_termino_energia[] = 0;
                $aux[] = 0;
            }
            // dd($db_EAct);
            if($tipo_count == 1)
            {
                if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
                {
                    // for ($i=0; $i < count($MES) ; $i++)
                    // {
                    //     for ($j=0; $j < count($aux_periodos); $j++)
                    //     {
                    //         if($aux[$j] <= $db_EAct[$j+($i*count($aux_periodos))])
                    //         {
                    //             $aux[$j] = $db_EAct[$j+($i*count($aux_periodos))];
                    //             $flag_aux = 1;
                    //         }
                    //     }
                    // }
                    for ($j=0; $j < count($aux_periodos); $j++)
                    {
                        if(isset($db_EAct[0][$j]))
                        {
                            if($aux[$j] <= $db_EAct[0][$j]->prom)
                            {
                                $aux[$j] = $db_EAct[0][$j];
                                $flag_aux = 1;
                            }
                        }
                    }
                }else{
                    // dd($db_EAct[0]);
                    for ($j=0; $j < count($aux_periodos); $j++)
                    {
                        if(isset($db_EAct[0][$j]))
                        {
                            if($aux[$j] <= $db_EAct[0][$j]->prom)
                            {
                                $aux[$j] = $db_EAct[0][$j];
                                $flag_aux = 1;
                            }
                        }
                    }
                }
            }
            // dd($db_coste_potencia);
            $P = array();
            // ALMACENA LA SUMATORIA DE LOS COSTOS DE POTENCIA DE ACUERDO A CADA
            // PERÍODO DENTRO DEL INTERVALO SELECCIONADO
            $total = 0;
            $total2 = 0;
            // dd($db_coste_potencia, $db_excesos);
            $i = 0;
            for ($i=0; $i < count($aux_periodos); $i++)
            {
                $aux_index = 'costeP'.($i+1);
                if(!empty($db_coste_potencia) && (isset($aux_coste_potencia[0][$aux_index]) && isset($aux_excesos[0][$aux_index])) )
                {
                    $coste_potencia[$i%count($aux_periodos)] = $coste_potencia[$i%count($aux_periodos)] + $aux_coste_potencia[0][$aux_index] + $aux_excesos[0][$aux_index];
                    $total = $aux_coste_potencia[0][$aux_index] + $total + $aux_excesos[0][$aux_index];
                }elseif(!empty($db_coste_potencia) && isset($aux_coste_potencia[0][$aux_index])){
                    $coste_potencia[$i%count($aux_periodos)] = $coste_potencia[$i%count($aux_periodos)] + $aux_coste_potencia[0][$aux_index];
                    $total = $aux_coste_potencia[0][$aux_index] + $total;
                }else{
                    $coste_potencia[$i] = 0;
                    $total = 0;
                }

                if(!empty($aux_energia) && isset($aux_energia[0][$aux_index]))
                {
                    $coste_termino_energia[$i%count($aux_periodos)] = $coste_termino_energia[$i%count($aux_periodos)] + $aux_energia[0][$aux_index];
                    $total2 = $aux_energia[0][$aux_index] + $total2;
                }else{
                    $coste_termino_energia[$i] = 0;
                    $total2 = 0;
                }

                if(!empty($db_p_contratada) && isset($db_p_contratada[0][$i]))
                {
                    $p_contratada[] = $db_p_contratada[0][$i]->p_contratada;
                }else{
                    $p_contratada[$i] = 0;
                }
            }

            array_push($coste_potencia, $total);
            array_push($coste_termino_energia, $total2);
            // if(Auth::user()->id == 18)
            //     dd($date_from, $date_to,$coste_potencia,$aux_coste_potencia);
            // dd($coste_potencia);
            $j = 0;
            if($flag_aux == 1)
            {
                if($tipo_count == 1)
                {
                    if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
                    {
                        // dd($aux);
                        foreach ($aux as $prom_EAct) {
                            if(is_null($prom_EAct->prom)|| !is_object($prom_EAct))
                            {
                                $EAct[] = 0;
                                continue;
                            }else{
                                $EAct[$j] = $prom_EAct->prom;
                            }
                            $i++;
                            $j++;
                        }
                    }else{
                        foreach ($aux as $prom_EAct) {
                            if(is_null($prom_EAct) || !is_object($prom_EAct))
                            {
                                $EAct[] = 0;
                                continue;
                            }else{
                                $EAct[$j] = $prom_EAct->prom;
                            }
                            $i++;
                            $j++;
                        }
                    }
                }

            }else{
                for ($i=0; $i < count($aux_periodos); $i++)
                {
                    $EAct[] = 0;
                }
            }
            // dd('eac',$EAct);
            // ******************************************************************
            // Calculo de la gráfica para Energía consumida Activa y Reactiva
            // ******************************************************************


            $Energia_Act_Reac_Consu = array();
            $db_Ener_Consu_Acti_Reacti[] = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("`Periodo`,SUM(`Energia Activa (kWh)`) E_Activa, SUM(`Energia Reactiva Inductiva (kVArh)`) E_Reac_Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) E_Reac_Cap"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

            $energia_activa_max = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("(SUM(`Energia Activa (kWh)`)) max_Activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->orderBy('max_Activa','DESC')->first();
            if(!is_null($energia_activa_max))
            {
                $energia_activa_max = $energia_activa_max->max_Activa;
            }
            else
                $energia_activa_max = 0;
        // dd('eac',$EAct);
            // ******************************************************************
            // Calculo de la gráfica para Venta de energia
            // ******************************************************************
            $db_Venta_Energia = array();
			$db_Venta_Costo_Energia = 1;
            $total_ventas = 0;
			
            if($contador2->tipo == 2 && $contador2->subtipo == 0)
            {
				/*$db_Venta_Costo_Energia = $db->table('Precio_Enegia_Generada')->select(\DB::raw("Periodo periodoP, precio totalPrice"))					
					->orderBy('Periodo', 'asc')					
					->get()
					->toArray();*/
                $db_Venta_Energia = $db->table('Venta_Energia_Activa')->select(\DB::raw("SUM(P1) ventaP1, SUM(P2) ventaP2, SUM(P3) ventaP3, SUM(P4) ventaP4, SUM(P5) ventaP5, SUM(P6) ventaP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                // dd($db_Venta_Energia);
                $index = 0;
                $aux_index = 'ventaP';
                foreach ($db_Venta_Energia as $venta) {
                    $aux_Venta_Energia[$index][$aux_index.($index+1)] = $venta->ventaP1;
                    $aux_Venta_Energia[$index][$aux_index.($index+2)] = $venta->ventaP2;
                    $aux_Venta_Energia[$index][$aux_index.($index+3)] = $venta->ventaP3;
                    $aux_Venta_Energia[$index][$aux_index.($index+4)] = $venta->ventaP4;
                    $aux_Venta_Energia[$index][$aux_index.($index+5)] = $venta->ventaP5;
                    $aux_Venta_Energia[$index][$aux_index.($index+6)] = $venta->ventaP6;
                    $index++;
                }

              //$generacion = $db->table('Generacion_Energia_Activa_y_Reactiva')->select(\DB::raw("Periodo,SUM(`Generación Energia Activa (kWh)`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                $generacion_datos = $db->table("ZPI_Contador_Festivos_Periodos")->select(\DB::raw("
                                        RIGHT(`Periodo`,1) as periodo,
                                        SUM(ABS(`EAct exp(kWh)`)) as generacion_energia_activa,
                                        SUM(ABS(`ERInd exp(kvarh)`)) as generacion_energia_inductiva,
                                        SUM(ABS(`ERCap imp(kvarh)`)) as generacion_energia_capacitiva
                                        "))
                                   ->where("date", ">=", $date_from)
                                   ->where("date", "<=", $date_to)
                                   ->groupBy('Periodo')->get();


            $generacion = array();

            for ($h=0; $h < 6; $h++)
            {
              $generacion[$h]['activa'] = 0;
              $generacion[$h]['inductiva'] = 0;
              $generacion[$h]['capacitiva'] = 0;
            }

            foreach ($generacion_datos as $value )
            {
              $h = intval($value->periodo)-1;
              $generacion[$h]['activa'] = $value->generacion_energia_activa;
              $generacion[$h]['inductiva'] = $value->generacion_energia_inductiva;
              $generacion[$h]['capacitiva'] = $value->generacion_energia_capacitiva;
            }

                $h = 0;
                if(!empty($db_Venta_Energia))
                {
                    foreach ($aux_Venta_Energia[$h] as $ventas) {
                        $total_ventas = $total_ventas + $ventas;
                        $h++;
                    }
                }
            }
			
			/*****************SUBTIPO 1 ************************/
 if($contador2->tipo == 2 && $contador2->subtipo == 1)
            {
	 
	 			$db_Venta_Costo_Energia = $db->table('Precio_Enegia_Generada')->select(\DB::raw("Periodo periodoP, precio totalPrice"))					
					->orderBy('Periodo', 'asc')					
					->get()
					->toArray();
	 
                $db_Venta_Energia = $db->table('ZPI_Contador_Festivos_Periodos')->select(\DB::raw("Periodo periodoP,SUM(`EAct exp(kWh)`) energySold"))
					->groupBy('Periodo')
					->orderBy('Periodo', 'asc')
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->get()
					->toArray();
                // dd($db_Venta_Energia);
                $index = 0;
	 			$indexIntern = 1;	
                $aux_index = 'ventaP';
	  if(!empty($db_Venta_Energia))
				{
							foreach ($db_Venta_Energia as $venta) 
							{

								//$aux_Venta_Energia[$index][$aux_index.($index+$indexIntern)] = ($venta->energySold * $db_Venta_Costo_Energia[$index]->totalPrice);
								
								if($venta->periodoP == "P1")
								{
									
									$total_ventas = $total_ventas + ($venta->energySold * $db_Venta_Costo_Energia[0]->totalPrice);
									
								}elseif($venta->periodoP == "P2"){
									
									$total_ventas = $total_ventas + ($venta->energySold * $db_Venta_Costo_Energia[1]->totalPrice);
									
								}elseif($venta->periodoP == "P3"){
									
									$total_ventas = $total_ventas + ($venta->energySold * $db_Venta_Costo_Energia[2]->totalPrice);
									
								}else{
									$total_ventas = $total_ventas + 0;
								}
								
								
								$index++;
								$indexIntern++;
							}
	 			 }

              //$generacion = $db->table('Generacion_Energia_Activa_y_Reactiva')->select(\DB::raw("Periodo,SUM(`Generación Energia Activa (kWh)`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                $generacion_datos = $db->table("ZPI_Contador_Festivos_Periodos")->select(\DB::raw("
                                        RIGHT(`Periodo`,1) as periodo,
                                        SUM(ABS(`EAct exp(kWh)`)) as generacion_energia_activa,
                                        SUM(ABS(`ERInd exp(kvarh)`)) as generacion_energia_inductiva,
                                        SUM(ABS(`ERCap imp(kvarh)`)) as generacion_energia_capacitiva
                                        "))
                                   ->where("date", ">=", $date_from)
                                   ->where("date", "<=", $date_to)
                                   ->groupBy('Periodo')->get();


            $generacion = array();

            for ($h=0; $h < 6; $h++)
            {
              $generacion[$h]['activa'] = 0;
              $generacion[$h]['inductiva'] = 0;
              $generacion[$h]['capacitiva'] = 0;
            }

            foreach ($generacion_datos as $value )
            {
              $h = intval($value->periodo)-1;
              $generacion[$h]['activa'] = $value->generacion_energia_activa;
              $generacion[$h]['inductiva'] = $value->generacion_energia_inductiva;
              $generacion[$h]['capacitiva'] = $value->generacion_energia_capacitiva;
            }

               /* $h = 0;
                if(!empty($db_Venta_Energia))
                {
                    foreach ($aux_Venta_Energia[$h] as $ventas) {
                        //$total_ventas = $total_ventas + $ventas;
                        $h++;
                    }
                }*/
            }



 /*****************SUBTIPO 1 ************************/
			
			
			
			

            if(empty($db_Ener_Consu_Acti_Reacti[0])){
                for ($i=0; $i < count($aux_periodos); $i++) {
                    $Energia_Act[$i] = 0;
                    $Energia_Reac_Induc[$i] = 0;
                    $Energia_Reac_Cap[$i] = 0;
                }
            }else
            {
                foreach ($db_Ener_Consu_Acti_Reacti[0] as $it) {
                    $Energia_Act[] = $it->E_Activa;
                    $Energia_Reac_Induc[] = $it->E_Reac_Induc;
                    $Energia_Reac_Cap[] = $it->E_Reac_Cap;
                }
            }
        }

        if($tipo_count < 3)
        {
            $potencia_contratada = $db->table('Potencia_Contratada')
                                      ->select(\DB::raw("Periodo as periodo, MAX(`Potencia_contratada`) as potencia_contratada,
                                                RIGHT(Periodo,1) as periodo_int"))
                                      ->where('date_start','<=',$date_from)->orWhere('date_end','>=',$date_to)
                                      ->groupBy('Periodo')->get();

            $vector_potencia = array();
            foreach($potencia_contratada as $potencia){
                $idx_periodo = intval($potencia->periodo_int) - 1;
                $vector_potencia[$idx_periodo] = doubleval($potencia->potencia_contratada);
            }

            $data_consumo = $db->table("ZPI_Contador_Festivos_Periodos")->select(\DB::raw("date, time, RIGHT(Periodo,1) as periodo,
                                    `EAct imp(kWh)` as energia_activa,
                                    ABS(`EAct exp(kWh)`) as generacion_energia_activa,
                                    `ERInd imp(kvarh)` as energia_reactiva_inductiva,
                                    ABS(`ERCap imp(kvarh)`) as energia_reactiva_capacitiva"))
                               ->where("date", ">=", $date_from)
                               ->where("date", "<=", $date_to)->get();

            $data_calculos = compact("date_from", "date_to", "interval", "data_consumo", "vector_potencia");
            $data_labels = $this->getLabelsPlot($data_calculos);
            $data_calculos["data_labels"] = $data_labels;

            $dataPlotting = $this->createConsumptionPlots($data_calculos);
        }
        elseif($tipo_count == 3)
        {
            $data_gnk = $db->table("Consumo_GN_Nm3")->select(\DB::raw("date, time, `Consumo GN (Nm3)` as consumo"))
                                    ->where("date", ">=", $date_from)
                                    ->where("date", "<=", $date_to)->get();
            $data_gnw = $db->table("Consumo_GN_kWh")->select(\DB::raw("date, time, `Consumo GN (kWh)` as consumo"))
                                    ->where("date", ">=", $date_from)
                                    ->where("date", "<=", $date_to)->get();

            $data_calculos = compact("date_from", "date_to", "interval", "data_gnk", "data_gnw");
            $data_labels = $this->getLabelsPlot($data_calculos);
            $data_calculos["data_labels"] = $data_labels;

            $dataPlotting = $this->createGasPlots($data_calculos);
        }

        // $db_coste_termino_energia = array();
        $contador_label = $contador2->count_label;

        // PARA GAS
        if($tipo_count == 3)
        {
            $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) coste_fijo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first()->coste_fijo;
            $coste_termino_variable = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) coste_variable"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first()->coste_variable;
            if(isset($db->table('Caudal_diario_contratado')->select(\DB::raw("`Caudal_diario_contratado` QD"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->QD))
                $QD_contratado = $db->table('Caudal_diario_contratado')->select(\DB::raw("`Caudal_diario_contratado` QD"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->QD;
            else
                $QD_contratado = 0;
            if(isset($db->table('Poder_calorifico_superior')->select(\DB::raw("PCS"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->PCS))
                $PCS = $db->table('Poder_calorifico_superior')->select(\DB::raw("PCS"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->PCS;
            else
                $PCS = 0;

            $tarifa = $db->table('Area_Cliente')->select(\DB::raw("`TARIFA` tarifa"))->first()->tarifa;
        }
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        // dd($consumo_activa, $consumo_capacitiva, $consumo_inductiva);

        \DB::disconnect('mysql2');

        $peri = count($aux_periodos);

        if(is_null($user->_perfil))
            $direccion = 'sin ubicación';
        else
            $direccion = $user->_perfil->direccion;
        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
            else
                $ctrl = 1;

            if($tipo_count < 3)
            {
                return view('resumen_energia_potencia.resumen_energia_potencia',compact('user','titulo','id','ctrl','periodos2','EAct','p_contratada','periodos_coste','coste_potencia','Energia_Act','Energia_Reac_Cap','Energia_Reac_Induc', 'coste_termino_energia','eje','consumo_activa','consumo_capacitiva','consumo_inductiva','label_intervalo','date_from','date_to','tipo_count','subtipo_count','db_Venta_Energia','db_Venta_Costo_Energia','total_ventas','generacion','interval','contador_label','domicilio','potencia_optima','dir_image_count','energia_activa_max','max_consumo_activa','balance2','tipo_tarifa','peri', 'dataSubperiodo', 'dataPlotting', 'contador2'));
            }else{
                return view('resumen_gn.resumen_gn',compact('user','titulo','id','ctrl','label_intervalo','date_from','date_to','tipo_count','interval','contador_label','direccion','coste_termino_fijo','coste_termino_variable','QD_contratado','PCS','tarifa','domicilio','dir_image_count','energia_activa_max','balance2','tipo_tarifa','peri', 'dataSubperiodo', 'dataPlotting', 'contador2'));
            }
        }
        return \Redirect::to('https://submeter.es/');
    }

    private function getDatesAnalysis($date_reference = null)
    {
        $interval = "";
        $session = Session::get('_flash');
        if(array_key_exists('intervalos', $session))
        {
            $interval = $session['intervalos'];
        }

        $monthsNames = array(1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril",
            5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre",
            10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre");
        if(!is_numeric($interval))
        {
            $interval = 2;
        }
        $date_label = "";

        switch ($interval){
            case 1:
                $date_from = Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $date_label = 'Ayer';
                break;
            case 2:
                $date_from = Carbon::now()->toDateString();
                $date_to = $date_from;
                $date_label = 'Hoy';
                break;
            case 3:
                $date_from = Carbon::now()->startOfWeek()->toDateString();
                $date_to = Carbon::now()->endOfWeek()->toDateString();
                $date_label = 'Semana Actual';
                break;
            case 4:
                $date_from = Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $date_label = 'Semana Anterior';
                break;
            case 5:
                $date_from = Carbon::now()->startOfMonth()->toDateString();
                $date_to = Carbon::now()->endOfMonth()->toDateString();
                $date_label = 'Mes Actual';
                break;
            case 6:
                $date_from = Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $date_label = 'Mes Anterior';
                break;
            case 7:
                $date_now = Carbon::now();
                $month = $date_now->month;
                $trimestre_actual = 3 + ceil($month/3);
                $trimestre_anterior = $trimestre_actual - 1;
                $diff_year = ceil($trimestre_anterior / 3);

                $trimestre = $trimestre_anterior % 4;
                if($diff_year == 1)
                {
                    $year = ($date_now->year - 1);
                    $monthBegin = 3 * ($trimestre) + 1;
                    $monthEnd = 3 * ($trimestre + 1);
                    $dateBegin = Carbon::createFromFormat("Y-n-d", $year."-".$monthBegin."-01");
                    $dateEnd = Carbon::createFromFormat("Y-n-d", $year."-".$monthEnd."-01");
                    $dateEnd->endOfMonth();
                } else {
                    $year = ($date_now->year);
                    $monthBegin = 3 * ($trimestre) + 1;
                    $monthEnd = 3 * ($trimestre + 1);
                    $dateBegin = Carbon::createFromFormat("Y-n-d", $year."-".$monthBegin."-01");
                    $dateEnd = Carbon::createFromFormat("Y-n-d", $year."-".$monthEnd."-01");
                    $dateEnd->endOfMonth();
                }
                $date_from = $dateBegin->toDateString();
                $date_to = $dateEnd->toDateString();
                $date_label = 'Ultimo Trimestre';
                break;
            case 10:
                $date_now = Carbon::now();
                $month = $date_now->month;
                $trimestre_actual = ceil($month/3) - 1;

                $year = $date_now->year;
                $monthBegin = 3 * ($trimestre_actual) + 1;
                $monthEnd = 3 * ($trimestre_actual + 1);
                $dateBegin = Carbon::createFromFormat("Y-n-d", $year."-".$monthBegin."-01");
                $dateEnd = Carbon::createFromFormat("Y-n-d", $year."-".$monthEnd."-01");
                $dateEnd->endOfMonth();

                $date_from = $dateBegin->toDateString();
                $date_to = $dateEnd->toDateString();
                $date_label = 'Trimestre Actual';
                break;
            case 8:
                $date_from = Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $date_label = 'Último Año';
                break;
            case 11:
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $date_label = 'Año Actual';
                break;
            case 9:
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $date_label = 'Personalizado';
                break;
            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $date_label = 'Hoy';
                break;
        }

        $dateInfo = array();
        $dateInfo["date_from"] = $date_from;
        $dateInfo["date_to"] = $date_to;
        $dateInfo["date_label"] = $date_label;
        return $dateInfo;
    }

    public static function createConsumptionPlots($data_calculos)
    {
        $interval = $data_calculos["interval"];
        $data_labels = $data_calculos["data_labels"];
        $data_consumo = $data_calculos["data_consumo"];

        $dataActiva = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataReactivaInductiva = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataReactivaCapacitiva = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataGeneracion = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataGeneracionN = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataBalance = array_fill_keys($data_labels["interval_keys"], 0.0);
        $total_activa = 0.0;
        $total_reactiva_inductiva = 0.0;
        $total_reactiva_capacitiva = 0.0;
        $total_generacion = 0.0;
        $max_energia_activa = 0.0;
        $max_energia_balance = 0.0;

        foreach($data_consumo as $data)
        {
            $keyData = ResumenEnergiaController::getKeyPlot($interval, $data->date, $data->time);
            if(array_key_exists($keyData, $dataActiva))
            {
                $dataActiva[$keyData] += $data->energia_activa;
                $total_activa += $data->energia_activa;
            }
            if(array_key_exists($keyData, $dataReactivaInductiva))
            {
                $dataReactivaInductiva[$keyData] += $data->energia_reactiva_inductiva;
                $total_reactiva_inductiva += $data->energia_reactiva_inductiva;
            }
            if(array_key_exists($keyData, $dataReactivaCapacitiva))
            {
                $dataReactivaCapacitiva[$keyData] += $data->energia_reactiva_capacitiva;
                $total_reactiva_capacitiva += $data->energia_reactiva_capacitiva;

            }
            if(array_key_exists($keyData, $dataGeneracion))
            {
                $dataGeneracion[$keyData] += $data->generacion_energia_activa;
                $total_generacion += $data->generacion_energia_activa;
                if($data->generacion_energia_activa > $max_energia_activa)
                {
                    $max_energia_activa = $data->generacion_energia_activa;
                }
            }
        }

        foreach($dataActiva as $keyData => $data_activa)
        {
            if($data_activa > $max_energia_activa)
            {
                $max_energia_activa = $data_activa;
            }
        }

        foreach($dataReactivaInductiva as $keyData => $data_reactiva_inductiva)
        {
            if($data_reactiva_inductiva > $max_energia_activa)
            {
                $max_energia_activa = $data_reactiva_inductiva;
            }
        }

        foreach($dataReactivaCapacitiva as $keyData => $data_reactiva_capacitiva)
        {
            if($data_reactiva_capacitiva > $max_energia_activa)
            {
                $max_energia_activa = $data_reactiva_capacitiva;
            }
        }

        foreach($dataGeneracion as $keyData => $data_generacion) {
            $dataGeneracionN[$keyData] = -$dataGeneracion[$keyData];
            $dataBalance[$keyData] = $dataActiva[$keyData] - $dataGeneracion[$keyData];
            if($dataBalance[$keyData] > $max_energia_balance)
            {
                $max_energia_balance = $dataBalance[$keyData];
            }
        }

        $plotActiva = array();
        $plotActiva["name"] = "Consumo Energía Activa";
        $plotActiva["suffix"] = "kWh";
        $plotActiva["time_label"] = $data_labels["aux_label"];
        $plotActiva["index_label"] = $data_labels["index_label"];
        $plotActiva["labels"] = json_encode($data_labels["interval_values"]);
        $plotActiva["series"] = array();

        $serie = array();
        $serie["name"] = "Energía Activa";
        $serie["color"] = "#004165";
        $serie["values"] = json_encode(array_values($dataActiva));
        $serie["aux_label"] = "Total de Energía Activa";
        $serie["total"] = $total_activa;
        $plotActiva["series"][] = $serie;

        $plotReactiva = array();
        $plotReactiva["name"] = "Consumo Energía Reactiva";
        $plotReactiva["suffix"] = "kVArh";
        $plotReactiva["time_label"] = $data_labels["aux_label"];
        $plotReactiva["index_label"] = $data_labels["index_label"];
        $plotReactiva["labels"] = json_encode($data_labels["interval_values"]);
        $plotReactiva["series"] = array();

        $serie = array();
        $serie["name"] = "Energía Reactiva Inductiva";
        $serie["color"] = "#B9C9D0";
        $serie["values"] = json_encode(array_values($dataReactivaInductiva));
        $serie["aux_label"] = "Total Consumo Inductiva";
        $serie["total"] = $total_reactiva_inductiva;
        $plotReactiva["series"][] = $serie;

        $serie = array();
        $serie["name"] = "Energía Reactiva Capacitiva";
        $serie["color"] = "#7D9AAA";
        $serie["values"] = json_encode(array_values($dataReactivaCapacitiva));
        $serie["aux_label"] = " \n\n\n\n\n\n\n\n\n\n\n\nTotal Consumo Capacitiva";
        $serie["total"] = $total_reactiva_capacitiva;
        $plotReactiva["series"][] = $serie;

        $plotGeneracion = array();
        $plotGeneracion["name"] = "Generación Energía";
        $plotGeneracion["suffix"] = "kWh";
        $plotGeneracion["time_label"] = $data_labels["aux_label"];
        $plotGeneracion["index_label"] = $data_labels["index_label"];
        $plotGeneracion["labels"] = json_encode($data_labels["interval_values"]);
        $plotGeneracion["series"] = array();

        $serie = array();
        $serie["name"] = "Generación Energía";
        $serie["color"] = "#B9C9D0";
        $serie["values"] = json_encode(array_values($dataGeneracion));
        $serie["aux_label"] = "Total Generación Energía";
        $serie["total"] = $total_generacion;
        $plotGeneracion["series"][] = $serie;

        $plotConsumo = array();
        $plotConsumo["time_label"] = $data_labels["aux_label"];
        $plotConsumo["labels"] = json_encode($data_labels["interval_values"]);
        $plotConsumo["max"] = $max_energia_balance;
        $plotConsumo["series"] = array();

        $serie = array();
        $serie["values"] = json_encode(array_values($dataActiva));
        $plotConsumo["series"][] = $serie;

        $serie = array();
        $serie["values"] = json_encode(array_values($dataReactivaInductiva));
        $plotConsumo["series"][] = $serie;

        $serie = array();
        $serie["values"] = json_encode(array_values($dataReactivaCapacitiva));
        $plotConsumo["series"][] = $serie;

        $plotBalance = array();
        $plotBalance["time_label"] = $data_labels["aux_label"];
        $plotBalance["labels"] = json_encode($data_labels["interval_values"]);
        $plotBalance["max"] = $max_energia_activa;
        $plotBalance["series"] = array();

        $serie = array();
        $serie["values"] = json_encode(array_values($dataActiva));
        $plotBalance["series"][] = $serie;

        $serie = array();
        $serie["values"] = json_encode(array_values($dataGeneracionN));
        $plotBalance["series"][] = $serie;

        $serie = array();
        $serie["values"] = json_encode(array_values($dataBalance));
        $plotBalance["series"][] = $serie;

        $dataPlotting = array();
        $dataPlotting["activa"] = $plotActiva;
        $dataPlotting["reactiva"] = $plotReactiva;
        $dataPlotting["generacion"] = $plotGeneracion;
        $dataPlotting["consumo"] = $plotConsumo;
        $dataPlotting["balance"] = $plotBalance;
        return $dataPlotting;
    }

    public static function createGasPlots($data_calculos)
    {
        $interval = $data_calculos["interval"];
        $data_labels = $data_calculos["data_labels"];
        $consumo_gnk = $data_calculos["data_gnk"];
        $consumo_gnw = $data_calculos["data_gnw"];

        $dataGNk = array_fill_keys($data_labels["interval_keys"], 0.0);
        $dataGNw = array_fill_keys($data_labels["interval_keys"], 0.0);
        $total_gnk = 0.0;
        $total_gnw = 0.0;

        foreach($consumo_gnk as $data)
        {
            $keyData = ResumenEnergiaController::getKeyPlot($interval, $data->date, $data->time);
            if(array_key_exists($keyData, $dataGNk))
            {
                $dataGNk[$keyData] += $data->consumo;
                $total_gnk += $data->consumo;
            }
        }

        foreach($consumo_gnw as $data)
        {
            $keyData = ResumenEnergiaController::getKeyPlot($interval, $data->date, $data->time);
            if(array_key_exists($keyData, $dataGNw))
            {
                $dataGNw[$keyData] += $data->consumo;
                $total_gnw += $data->consumo;
            }
        }

        $dataGNk = array_values($dataGNk);
        $dataGNw = array_values($dataGNw);

        $plotGnk = array();
        $plotGnk["labels"] = json_encode($data_labels["interval_values"]);
        $plotGnk["time_label"] = $data_labels["aux_label"];
        $plotGnk["total"] = $total_gnk;
        $plotGnk["series"] = array();

        $serie = array();
        $serie["values"] = json_encode(array_values($dataGNk));
        $plotGnk["series"][] = $serie;

        $plotGnw = array();
        $plotGnw["labels"] = json_encode($data_labels["interval_values"]);
        $plotGnw["time_label"] = $data_labels["aux_label"];
        $plotGnw["total"] = $total_gnw;
        $plotGnw["series"] = array();

        $serie = array();
        $serie["values"] = json_encode(array_values($dataGNw));
        $serie["total"] = $total_gnw;
        $plotGnw["series"][] = $serie;

        $dataPlotting = array();
        $dataPlotting["gasGNk"] = $plotGnk;
        $dataPlotting["gasGNw"] = $plotGnw;
        return $dataPlotting;
    }

    public static function getLabelsPlot($data_calculos)
    {
        $date_from = $data_calculos["date_from"];
        $date_to = $data_calculos["date_to"];
        $interval = $data_calculos["interval"];

        $monthsNames = array(1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril",
            5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre",
            10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre");

        $daysNames = array("1"=>"Lunes", "2"=>"Martes", "3"=>"Miercoles",
            "4"=>"Jueves","5"=>"Viernes", "6"=>"Sabado", "7"=>"Domingo");

        $aux_label = "";
        $aux_interval = "";
        $index_label = "";
        switch ($interval){
            case 1:
                $date_label = 'Ayer';

                $period = new CarbonPeriod($date_from." 00:00:00", '1 hour', $date_to." 23:59:00");
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H");
                    $interval_values[] = $date->format("H:00");
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Hora";
                break;
            case 2:
                $date_label = 'Hoy';

                $period = new CarbonPeriod($date_from." 00:00:00", '1 hour', $date_to." 23:59:00");
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H");
                    $interval_values[] = $date->format("H:00");
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Hora";
                break;
            case 3:
                $date_label = 'Semana Actual';

                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $daysNames[$date->dayOfWeekIso];
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Día";
                break;
            case 4:
                $date_label = 'Semana Anterior';

                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $daysNames[$date->dayOfWeekIso];
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Día";
                break;
            case 5:
                $date_label = 'Mes Actual';

                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $date->day;
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Día";
                break;
            case 6:
                $date_label = 'Mes Anterior';

                $interval_keys = array();
                $interval_values = array();

                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $date->day;
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Día";
                break;
            case 7:
                $date_label = 'Ultimo Trimestre';

                $interval_keys = array();
                $interval_values = array();

                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $index_label = "{y}";
                $aux_label = "Mes";
                break;
            case 10:
                $date_label = 'Trimestre Actual';

                $interval_keys = array();
                $interval_values = array();

                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $index_label = "{y}";
                $aux_label = "Mes";
                break;
            case 8:
                $date_label = 'Último Año';

                $interval_keys = array();
                $interval_values = array();

                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Mes";
                break;
            case 11:
                $date_label = 'Año Actual';

                $interval_keys = array();
                $interval_values = array();


                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Mes";
                break;
            case 9:
                $date_label = 'Personalizado';

                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $date->format("Y-m-d");
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                break;
            default:
                $date_label = 'Hoy';

                $period = new CarbonPeriod($date_from." 00:00:00", '1 hour', $date_to." 23:59:00");
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H");
                    $interval_values[] = $date->format("H:00");
                }
                $data_keys = $interval_keys;
                $data_values = $interval_values;
                $aux_label = "Hora";
                break;
        }

        $dateInterval = array();
        $dateInterval["interval_keys"] = $interval_keys;
        $dateInterval["interval_values"] = $interval_values;
        $dateInterval["data_keys"] = $data_keys;
        $dateInterval["data_values"] = $data_values;
        $dateInterval["aux_label"] = $aux_label;
        $dateInterval["index_label"] = $index_label;
        return $dateInterval;
    }

    private function getInfoSubPeriodo($dataPeriodo)
    {
        $interval = $dataPeriodo['interval'];
        $date_from = $dataPeriodo['date_from'];
        $date_to = $dataPeriodo['date_to'];

        if($interval == 8 || $interval == 11)
        {
            $periods = CarbonPeriod::create($date_from, '1 month', $date_to);
            $begin_periods = [];
            $end_periods = [];
            foreach($periods as $date)
            {
                $date->addMonth(-3);
                $month = floor(($date->month - 1) / 3);
                $date_begin = $date->year."-".(3 * $month + 1)."-01";
                $date_c = Carbon::createFromFormat("Y-m-d", $date_begin);
                $date_begin = $date_c->toDateString();
                $date_end = $date->year."-".(3 * $month + 3)."-01";
                $date_c = Carbon::createFromFormat("Y-m-d", $date_end);
                $date_end = $date_c->endOfMonth()->toDateString();
                $begin_periods[] = $date_begin;
                $end_periods[] = $date_end;
            }
            $label = "Trimestre Actual";
        }
        else if($interval == 7 || $interval == 10)
        {
            $begin_periods = [];
            $end_periods = [];
            $periods = CarbonPeriod::create($date_from, '1 month', $date_to);
            foreach($periods as $date)
            {
                $date->addMonth(-1);
                $date_begin = $date->startOfMonth()->toDateString();
                $date_end = $date->endOfMonth()->toDateString();
                $begin_periods[] = $date_begin;
                $end_periods[] = $date_end;
            }
            $label = "Mes Actual";
        }
        else if($interval >= 3 && $interval <= 6)
        {
            $begin_periods = [];
            $end_periods = [];
            $periods = CarbonPeriod::create($date_from, '1 day', $date_to);
            foreach($periods as $date)
            {
                $date->addDay(-1);
                $date_begin = $date->toDateString();
                $date_end = $date->toDateString();
                $begin_periods[] = $date_begin;
                $end_periods[] = $date_end;
            }
            $label = "Ayer";
        }
        else
        {
            $begin_periods = [];
            $end_periods = [];
            $label = "N/A";
        }

        $dataSubperiod = [];
        $dataSubperiod["begin_periods"] = $begin_periods;
        $dataSubperiod["end_periods"] = $end_periods;
        $dataSubperiod["label"] = $label;
        return $dataSubperiod;
    }

    public static function getKeyPlot($interval, $date, $time)
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s", $date." ".$time);
        $key = "";
        switch ($interval){
            case 1:
                $date_label = 'Ayer';
                $key = $date->format("Y-m-d-H");
                break;
            case 2:
                $date_label = 'Hoy';
                $key = $date->format("Y-m-d-H");
                break;
            case 3:
                $date_label = 'Semana Actual';
                $key = $date->format("Y-m-d");
                break;
            case 4:
                $date_label = 'Semana Anterior';
                $key = $date->format("Y-m-d");
                break;
            case 5:
                $date_label = 'Mes Actual';
                $key = $date->format("Y-m-d");
                break;
            case 6:
                $date_label = 'Mes Anterior';
                $key = $date->format("Y-m-d");
                break;
            case 7:
                $date_label = 'Ultimo Trimestre';
                $key = $date->format("Y-m");
                break;
            case 10:
                $date_label = 'Trimestre Actual';
                $key = $date->format("Y-m");
                break;
            case 8:
                $date_label = 'Último Año';
                $key = $date->format("Y-m");
                break;
            case 11:
                $date_label = 'Año Actual';
                $key = $date->format("Y-m");
                break;
            case 9:
                $date_label = 'Personalizado';
                $key = $date->format("Y-m-d");
                break;
            default:
                $date_label = 'Hoy';
                $key = $date->format("Y-m-d-H");
                break;
        }
        return $key;
    }




    public function calcular_exceso($date_from,$date_to){



    }



}
