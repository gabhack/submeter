<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;

use App\Count;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->tipo === 1) {
            return redirect()->route("enterprise.index");
        }

        // $eje = array();
        // $consumo_activa = array();
        // $consumo_capacitiva = array();
        // $consumo_inductiva = array();
        // $generacion = array();
        // $max_consumo_activa = 0;  
        // $balance2 = array();  

        $contador = null;
        $session = $request->session()->all();
        if (array_key_exists("_flash", $session)) {
            $flash = $session['_flash'];
            if (array_key_exists("current_count", $flash)) {
                $count_label = $flash['current_count'];
                $contador = Count::where('count_label', $count_label)->first();
            }
        }

        if ($contador == null) {
            $contador = Count::where('user_id', $user->id)->first();
        }

        if ($user->tipo != 1) {
            //Se redirige dependiendo del home que tenga asiganado [Sonia Workana]
            //Esto no se aplica para los usuarios de tipo 1
            switch ($user->home) {
                case 'home_1':
                    $route = redirect()->route('resumen.energia.potencia', [$user->id]);
                    break;
                case 'home_2':
                    $route = redirect()->route('ver.panel.user', [$user->id]);
                    break;
                case 'home_3':
                    $route = redirect()->route('consumo.energia', [$user->id]);
                    break;
                case 'home_4': //Generacion de energia
                    $route = redirect()->route('consumo.energia', [$user->id]);
                    break;
                case 'home_5':
                    $route = redirect()->route('analisis.potencia2', [$user->id]);
                    break;
                case 'home_6': // Simulación de Potencia
                    $route = redirect()->route('simulacion.potencia', [$user->id]);
                    break;
                case 'home_7': // Mercado Energético
                    $route = redirect()->route('mercado.energetico', [$user->id]);
                    break;
                case 'home_8': // Seguimiento de Objetivos
                    $route = redirect()->route('seguimiento.objetivos', [$user->id]);
                    break;
                case 'home_9': // Comparador de ofertas
                    $route = redirect()->route('comparador.ofertas', [$user->id]);
                    break;
                case 'home_10': // Simulación de Facturas
                    $route = redirect()->route('simulacion.facturas', [$user->id]);
                    break;
                case 'home_11': // Informes y Alertas
                    $route = redirect()->route('informes.periodicos.alertas', [$user->id]);
                    break;
                case 'home_12': // Emisiones CO2
                    $route = redirect()->route('emisiones.co2', [$user->id]);
                    break;
                case 'home_13': // Exportación de datos
                    $route = redirect()->route('exportar.datos', [$user->id]);
                    break;
                case 'home_14': // Analizadores Submetering
                    $route = redirect()->route('analyzersgroup', [$user->id]);
                    break;
                case 'home_15': // Producción Submetering
                    $route = redirect()->route('statistics.resume', ['type' => 'produccion', 'user_id' => $user->id]);
                    break;
                case 'home_16': // Indicadores Energéticos
                    $route = redirect()->route('statistics.resume', ['type' => 'indicadores', 'user_id' => $user->id]);
                    break;
                case 'home_17': // Representación datos
                    $route = redirect()->route('statistics.resume', ['type' => 'representacion', 'user_id' => $user->id]);
                    break;
                case 'home_18': // Control de Accesos
                    $route = redirect()->route('logaccesos', [$user->id]);
                    break;
                case 'home_19': // Área Cliente
                    $route = redirect()->route('area.cliente', [$user->id]);
                    break;
                case 'home_20': // Área Cliente
                    $route = redirect('https://help.submeter.es');
                    break;
                default:
                    $route = redirect()->route('resumen.energia.potencia', [$user->id]);
                    break;
            }
            return $route;
        }
        // else 
        // {
        //     $titulo = 'Resumen de Contadores';
        //     $tipo_count = 0;
        //     $tipo_tarifa = 1;
        //     $date_from = 'date_from';
        //     $date_to = 'date_to';

        //     if($contador && property_exists($contador, "tarifa"))
        //     {
        //         $tipo_tarifa = $contador->tarifa;
        //     }
        //     return view('Dashboard.dashboard',compact('user','titulo','tipo_count','tipo_tarifa','date_from','date_to'))
        //     ->with( 'maps_url', '' )
        //         ->with( 'markers', null );
        // }

        // if($user->tipo != 1)
        // {
        //     $titulo = 'Resumen de Contadores';
        //     $interval = Session::get('_flash')['intervalos'];
        //     $id = $user->id;
        //     $ctrl = 0;
        //     $tipo_count = Count::where('user_id',$id)->first()->tipo;
        //     $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

        //     if(empty($contador))
        //     {
        //         $contador2 = Count::where('user_id',$id)->first();

        //     }else{
        //         $contador2 = Count::where('count_label',$contador)->first();
        //     }

        //     $sesion = $request->session()->all();
        //     $flash = $sesion['_flash'];
        //     $flash['current_count'] = $contador2->count_label;
        //     Session::put('_flash',$flash);
        //     $url = Session::get('_previous')['url'];

        //     config(['database.connections.mysql2.host' => $contador2->host]);
        //     config(['database.connections.mysql2.port' => $contador2->port]);
        //     config(['database.connections.mysql2.database' => $contador2->database]);
        //     config(['database.connections.mysql2.username' => $contador2->username]);
        //     config(['database.connections.mysql2.password' => $contador2->password]);
        //     env('MYSQL2_HOST',$contador2->host);
        //     env('MYSQL2_DATABASE',$contador2->database);
        //     env('MYSQL2_USERNAME', $contador2->username);
        //     env('MYSQL2_PASSWORD',$contador2->password);
        //     try {
        //         \DB::connection('mysql2')->getPdo();
        //     } catch (\Exception $e) {
        //         session()->flush('NADA');
        //         Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Contacte a su administrador.");               
        //         return \Redirect::back();
        //     }

        //     $db = \DB::connection('mysql2');            

        //     $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();
        //     // dd($domicilio->suministro_del_domicilio);

        //     switch ($interval) {
        //         case '1':
        //         $date_from = \Carbon\Carbon::yesterday()->toDateString();
        //         $date_to = $date_from;
        //         $label_intervalo = 'Ayer';
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }               
        //         // if($tipo_count == 1)
        //         // {
        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date',$date_from)->groupBy('Hora')->get();
        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;
        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 $consumo_inductiva[] = $consu->inductiva;
        //                 $consumo_capacitiva[] = $consu->capacitiva;                    
        //             } 
        //         // }else{
        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date',$date_from)->groupBy('time')->get();
        //             $t = 0;
        //             foreach ($balance as $val) 
        //             {
        //                 if(isset($val->eje))
        //                 {
        //                     $balance2[$t]['eje'] = $val->eje;
        //                     $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     // break;
        //                 }else{
        //                     $balance2[$t]['eje'] = $t+1;
        //                     $balance2[$t]['consumo_energia'] = 0;
        //                     $balance2[$t]['generacion_energia'] = 0;
        //                     $balance2[$t]['balance_neto'] = 0;
        //                 }
        //                 $t++;
        //             }
        //             // dd($balance2, $balance, $eje);
        //         // }

        //     break;

        //     case '3':
        //         $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }
        //         $label_intervalo = 'Semana Actual';
        //         $eje[0] = 'Lunes';
        //         $eje[1] = 'Martes';
        //         $eje[2] = 'Miércoles';
        //         $eje[3] = 'Jueves';
        //         $eje[4] = 'Viernes';
        //         $eje[5] = 'Sábado';
        //         $eje[6] = 'Domingo';

        //         // if($tipo_count == 1)
        //         // {
        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;
        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 $consumo_inductiva[] = $consu->inductiva;
        //                 $consumo_capacitiva[] = $consu->capacitiva;                    
        //             }
        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
        //             $t = 0;                    
        //             foreach ($balance as $val) 
        //             {
        //                 // if($val->eje == $eje[$t])
        //                 // {
        //                     $balance2[$t]['eje'] = $val->eje;
        //                     $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     $t++;
        //                 //     break;
        //                 // }else{
        //                 //     $balance2[$t]['consumo_energia'] = 0;
        //                 //     $balance2[$t]['generacion_energia'] = 0;
        //                 //     $balance2[$t]['balance_neto'] = 0;
        //                 // }
        //             }
        //             // dd($balance,$balance2);
        //         // }
        //     break;

        //     case '4':
        //         $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }
        //         $label_intervalo = 'Semana Anterior';
        //         $eje[0] = 'Lunes';
        //         $eje[1] = 'Martes';
        //         $eje[2] = 'Miércoles';
        //         $eje[3] = 'Jueves';
        //         $eje[4] = 'Viernes';
        //         $eje[5] = 'Sábado';
        //         $eje[6] = 'Domingo';

        //         // if($tipo_count == 1)
        //         // {
        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;
        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 $consumo_inductiva[] = $consu->inductiva;
        //                 $consumo_capacitiva[] = $consu->capacitiva;                    
        //             }
        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
        //             $t = 0;                    
        //             foreach ($balance as $val) 
        //             {
        //                 // if($val->eje == $eje[$t])
        //                 // {
        //                     $balance2[$t]['eje'] = $val->eje;
        //                     $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     $t++;
        //                 //     break;
        //                 // }else{
        //                 //     $balance2[$t]['consumo_energia'] = 0;
        //                 //     $balance2[$t]['generacion_energia'] = 0;
        //                 //     $balance2[$t]['balance_neto'] = 0;
        //                 // }
        //             }                 
        //         // }
        //     break;

        //     case '5':
        //         $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
        //         $label_intervalo = 'Mes Actual';
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }
        //         // if($tipo_count == 1)
        //         // {

        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;
        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 $consumo_inductiva[] = $consu->inductiva;
        //                 $consumo_capacitiva[] = $consu->capacitiva;                    
        //             }
        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("DAY(date) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
        //             $t = 0;
        //             foreach ($balance as $val) 
        //             {
        //                 if(isset($val->eje))
        //                 {
        //                     $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     // break;
        //                 }else{
        //                     $balance2[$t]['eje'] = $t+1;
        //                     $balance2[$t]['consumo_energia'] = 0;
        //                     $balance2[$t]['generacion_energia'] = 0;
        //                     $balance2[$t]['balance_neto'] = 0;
        //                 }
        //                 $t++;
        //             }
        //         // }
        //     break;

        //     case '6':
        //         $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }
        //         $label_intervalo = 'Mes Anterior';


        //         // if($tipo_count == 1)
        //         // {

        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

        //             // dd($consumo_diario_energia);
        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;

        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 if($consu->inductiva >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->inductiva;
        //                 if($consu->capacitiva >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->capacitiva;

        //                     $consumo_inductiva[] = $consu->inductiva;
        //                     $consumo_capacitiva[] = $consu->capacitiva;
        //             }
        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("DAY(date) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
        //             $t = 0;
        //             foreach ($balance as $val) 
        //             {
        //                 if(isset($val->eje))
        //                 {
        //                     $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     // break;
        //                 }else{
        //                     $balance2[$t]['eje'] = $t+1;
        //                     $balance2[$t]['consumo_energia'] = 0;
        //                     $balance2[$t]['generacion_energia'] = 0;
        //                     $balance2[$t]['balance_neto'] = 0;
        //                 }
        //                 $t++;
        //             }
        //         // }

        //     break;

        //     case '7':
        //         $now = \Carbon\Carbon::now()->month;
        //         $dont = 0;
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
        //         {
        //             $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //             $dont = 1;
        //             // dd($now,$date_from,$date_to);
        //         }
        //         if($dont == 0)
        //         {
        //             if($now == 1 || $now == 2 || $now == 3)
        //             {
        //                 if($dont == 0)
        //                 {
        //                     $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
        //                     $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
        //                 }
        //                 $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //             }elseif($now == 4 || $now == 7 || $now == 10){
        //                 if($dont == 0)
        //                 {
        //                     $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
        //                     $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
        //                 }
        //                 if($now == 4)
        //                 {
        //                     $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 7){
        //                     $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 10){
        //                     $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }
        //             }elseif($now == 5 || $now == 8 || $now == 11){
        //                 if($dont == 0)
        //                 {
        //                     $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
        //                     $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
        //                 }
        //                 if($now == 5)
        //                 {
        //                     $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 8){
        //                     $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 11){
        //                     $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }
        //             }elseif($now == 6 || $now == 9 || $now == 12){
        //                 if($dont == 0)
        //                 {
        //                     $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
        //                     $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
        //                 }
        //                 if($now == 6)
        //                 {
        //                     $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 9){
        //                     $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }elseif($now == 12){
        //                     $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
        //                     $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 }
        //             }
        //         }else{
        //             // dd($now);
        //             if($now == 1)
        //             {
        //                 $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
        //             }elseif($now == 4){
        //                 $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
        //             }elseif($now == 7){                                    
        //                 $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //             }elseif($now == 10){
        //                 $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //                 $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
        //             }
        //         }
        //         $label_intervalo = 'Ultimo Trimestre';
        //         if($tipo_count < 3)
        //         {
        //             //$potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,Potencia_contratada p_optima"))->orderBy('eje')->get()->toArray();

        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();


        //             $band = 0;
        //             for ($t=0; $t < 3; $t++) {                                
        //                 foreach ($consumo_diario_energia as $val) 
        //                 {
        //                     $band = 1;
        //                     if(!empty($val) || !is_null($val))
        //                     {                                        
        //                         if($val->eje == $eje[$t])
        //                         {
        //                             $consumo_activa[$t] = $val->activa;
        //                             if($val->activa >= $max_consumo_activa)
        //                                 $max_consumo_activa = $val->activa;
        //                             if($val->inductiva >= $max_consumo_activa)
        //                                 $max_consumo_activa = $val->inductiva;
        //                             if($val->capacitiva >= $max_consumo_activa)
        //                                 $max_consumo_activa = $val->capacitiva;

        //                             $consumo_inductiva[$t] = $val->inductiva;
        //                             $consumo_capacitiva[$t] = $val->capacitiva;
        //                             break;
        //                         }else{
        //                             $consumo_activa[$t] = 0;
        //                             $consumo_inductiva[$t] = 0;
        //                             $consumo_capacitiva[$t] = 0;    
        //                         }
        //                     }else{
        //                         $consumo_activa[$t] = 0;
        //                         $consumo_inductiva[$t] = 0;
        //                         $consumo_capacitiva[$t] = 0;
        //                     }                                    
        //                 }
        //                 if($band == 0)
        //                 {
        //                     $consumo_activa[$t] = 0;
        //                     $consumo_inductiva[$t] = 0;
        //                     $consumo_capacitiva[$t] = 0;
        //                 }
        //             }
        //             // dd($consumo_diario_energia,$consumo_activa,$consumo_capacitiva, $consumo_inductiva);

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //             for ($t=0; $t < 3; $t++)
        //             {
        //                 foreach ($balance as $val) 
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $balance2[$t]['eje'] = $val->eje;
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         break;
        //                     }else{
        //                         $balance2[$t]['eje'] = $eje[$t];
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                 }                        
        //             }
        //             // dd($balance, $balance2);
        //         }else{
        //             $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

        //             $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //         }

        //     break;

        //     case '10':
        //         $now = \Carbon\Carbon::now()->month;
        //         $eje = array();
        //         $dont = 0;
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
        //         {
        //             $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //             $dont = 1;
        //         }
        //         if($now == 1 || $now == 2 || $now == 3)
        //         {
        //             if($dont == 0)
        //             {
        //                 $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
        //                 $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
        //             }
        //             $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
        //             $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
        //             $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
        //         }elseif($now == 4 || $now == 5 || $now == 6){
        //             if($dont == 0)
        //             {
        //                 $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
        //                 $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
        //             }
        //             $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
        //             $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
        //             $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
        //         }elseif($now == 7 || $now == 8 || $now == 9){
        //             if($dont == 0)
        //             {
        //                 $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
        //                 $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
        //             }
        //             $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
        //             $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
        //             $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
        //         }elseif($now == 10 || $now == 11 || $now == 12){
        //             if($dont == 0)
        //             {
        //                 $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
        //                 $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
        //             }
        //             $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
        //             $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
        //             $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
        //         }
        //         $label_intervalo = 'Trimestre Actual';
        //         if($tipo_count < 3)
        //         {
        //             //$potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,Potencia_contratada p_optima"))->orderBy('eje')->get()->toArray();

        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();


        //             for ($t=0; $t < 3; $t++)
        //             {
        //                 foreach ($consumo_diario_energia as $val)
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $consumo_activa[$t] = $val->activa;

        //                         if($val->activa >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->activa;
        //                         if($val->inductiva >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->inductiva;
        //                         if($val->capacitiva >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->capacitiva;

        //                         $consumo_inductiva[$t] = $val->inductiva;
        //                         $consumo_capacitiva[$t] = $val->capacitiva;    
        //                         break;
        //                     }else{
        //                         $consumo_activa[$t] = 0;
        //                         $consumo_inductiva[$t] = 0;
        //                         $consumo_capacitiva[$t] = 0;
        //                     }
        //                 }
        //             }

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

        //             for ($t=0; $t < 3; $t++) 
        //             {
        //                 foreach ($balance as $val) 
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $balance2[$t]['eje'] = $val->eje;
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         break;
        //                     }else{
        //                         $balance2[$t]['eje'] = $eje[$t];
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                 }                        
        //             }
        //             // dd($eje);
        //         }else{
        //             $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

        //             $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //         }

        //     break;

        //     case '11':
        //         $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
        //         $dont = 0;
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //             $dont = 1;
        //         }
        //         $label_intervalo = 'Año Actual';
        //         if($dont == 0)
        //         {
        //             $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //             $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
        //         }else{
        //             $a_o = \Carbon\Carbon::parse($date_from)->year;
        //             $eje[0] = "Enero(".$a_o.")";
        //             $eje[1] = "Febrero(".$a_o.")";
        //             $eje[2] = "Marzo(".$a_o.")";
        //             $eje[3] = "Abril(".$a_o.")";
        //             $eje[4] = "Mayo(".$a_o.")";
        //             $eje[5] = "Junio(".$a_o.")";
        //             $eje[6] = "Julio(".$a_o.")";
        //             $eje[7] = "Agosto(".$a_o.")";
        //             $eje[8] = "Septiembre(".$a_o.")";
        //             $eje[9] = "Octubre(".$a_o.")";
        //             $eje[10] = "Noviembre(".$a_o.")";
        //             $eje[11] = "Diciembre(".$a_o.")";
        //         }

        //         // dd($eje);

        //         // if($tipo_count == 1)
        //         // {

        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //             for ($t=0; $t < 12; $t++)
        //             {
        //                 foreach ($consumo_diario_energia as $val)
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $consumo_activa[$t] = $val->activa;

        //                         if($val->activa >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->activa;
        //                         if($val->inductiva >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->inductiva;
        //                         if($val->capacitiva >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->capacitiva;

        //                             $consumo_inductiva[$t] = $val->inductiva;
        //                             $consumo_capacitiva[$t] = $val->capacitiva;
        //                             break;
        //                     }else{
        //                         $consumo_activa[$t] = 0;
        //                         $consumo_inductiva[$t] = 0;
        //                         $consumo_capacitiva[$t] = 0;
        //                     }
        //                 }
        //             }
        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //             for ($t=0; $t < 12; $t++) 
        //             {
        //                 foreach ($balance as $val) 
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $balance2[$t]['eje'] = $val->eje;
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         break;
        //                     }else{
        //                         $balance2[$t]['eje'] = $eje[$t];
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                 }                        
        //             }
        //             // dd($balance2);
        //         // }

        //     break;

        //     case '8':
        //         $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
        //         $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();                
        //         $dont = 0;
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //             $dont = 1;
        //         }
        //         $label_intervalo = 'Último Año';
        //         if($dont == 0)
        //         {
        //             $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
        //             $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";                            
        //         }else{
        //             $a_o = \Carbon\Carbon::parse($date_from)->year;
        //             $eje[0] = "Enero(".$a_o.")";
        //             $eje[1] = "Febrero(".$a_o.")";
        //             $eje[2] = "Marzo(".$a_o.")";
        //             $eje[3] = "Abril(".$a_o.")";
        //             $eje[4] = "Mayo(".$a_o.")";
        //             $eje[5] = "Junio(".$a_o.")";
        //             $eje[6] = "Julio(".$a_o.")";
        //             $eje[7] = "Agosto(".$a_o.")";
        //             $eje[8] = "Septiembre(".$a_o.")";
        //             $eje[9] = "Octubre(".$a_o.")";
        //             $eje[10] = "Noviembre(".$a_o.")";
        //             $eje[11] = "Diciembre(".$a_o.")";
        //         }

        //         if($tipo_count < 3)
        //         {
        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //             for ($t=0; $t < 12; $t++) 
        //             {
        //                 foreach ($consumo_diario_energia as $val) 
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $consumo_activa[$t] = $val->activa;
        //                         if($val->activa >= $max_consumo_activa)
        //                             $max_consumo_activa = $val->activa;
        //                         $consumo_inductiva[$t] = $val->inductiva;
        //                         $consumo_capacitiva[$t] = $val->capacitiva;
        //                         break;
        //                     }else{
        //                         $consumo_activa[$t] = 0;
        //                         $consumo_inductiva[$t] = 0;
        //                         $consumo_capacitiva[$t] = 0;                                
        //                     }
        //                 }                        
        //             }

        //             // foreach ($consumo_diario_energia as $consu) {
        //             //     $eje[] = $consu->eje;
        //             //     $consumo_activa[] = $consu->activa;
        //             //     if($consu->activa >= $max_consumo_activa)
        //             //         $max_consumo_activa = $consu->activa;
        //             //     $consumo_inductiva[] = $consu->inductiva;
        //             //     $consumo_capacitiva[] = $consu->capacitiva;                    
        //             // }

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //             for ($t=0; $t < 12; $t++) 
        //             {
        //                 foreach ($balance as $val) 
        //                 {
        //                     if($val->eje == $eje[$t])
        //                     {
        //                         $balance2[$t]['eje'] = $val->eje;
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         break;
        //                     }else{
        //                         $balance2[$t]['eje'] = $eje[$t];
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                 }                        
        //             }
        //         }else{
        //             $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

        //             $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
        //         }

        //     break;

        //     case '9':
        //         $date_from = Session::get('_flash')['date_from_personalice'];
        //         $date_to = Session::get('_flash')['date_to_personalice'];
        //         $label_intervalo = 'Personalizado';
        //         $dates = [];
        //         $date_from_Car = \Carbon\Carbon::parse($date_from);
        //         $date_to_Car = \Carbon\Carbon::parse($date_to);
        //         $totalActiva = 0;

        //         if($tipo_count < 3)
        //         {
        //             if($date_to != $date_from)
        //             {
        //                 for($date = $date_from_Car; $date->lte($date_to_Car); $date->addDay()) {
        //                     $dates[] = $date->format('Y-m-d');
        //                 }
        //                 $eje = $dates;
        //                 $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("date eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
        //                 for($t = 0; $t < count($dates); $t++)
        //                 {
        //                     if(isset($consumo_diario_energia[$t]->activa))
        //                     {
        //                         $consumo_activa[$t] = $consumo_diario_energia[$t]->activa;
        //                         $totalActiva += $consumo_diario_energia[$t]->activa;
        //                         if($consumo_diario_energia[$t]->activa >= $max_consumo_activa)
        //                             $max_consumo_activa = $consumo_diario_energia[$t]->activa;
        //                         $consumo_inductiva[$t] = $consumo_diario_energia[$t]->activa;
        //                         $consumo_capacitiva[$t] = $consumo_diario_energia[$t]->activa;
        //                     }else{
        //                         $consumo_activa[$t] = 0;
        //                         $consumo_inductiva[$t] = 0;
        //                         $consumo_capacitiva[$t] = 0;
        //                         $totalActiva += 0;
        //                     }
        //                 } 

        //                 // foreach ($consumo_diario_energia as $consu) {
        //                 //     $eje[] = $consu->eje;
        //                 //     $consumo_activa[] = $consu->activa;
        //                 //     if($consu->activa >= $max_consumo_activa)
        //                 //         $max_consumo_activa = $consu->activa;
        //                 //     $consumo_inductiva[] = $consu->inductiva;
        //                 //     $consumo_capacitiva[] = $consu->capacitiva;                    
        //                 // }

        //                 $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("date eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();                        
        //                 for ($t=0; $t < count($dates); $t++) 
        //                 {
        //                     if(isset($balance[$t]->generacion_energia))
        //                     {
        //                         $balance2[$t]['consumo_energia'] = $balance[$t]->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $balance[$t]->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $balance[$t]->balance_neto;
        //                     }else{
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                     // foreach ($balance as $val) 
        //                     // {
        //                     //     if($val->eje == $eje[$t])
        //                     //     {
        //                     //         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                     //         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                     //         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                     //         break;
        //                     //     }else{
        //                     //         $balance2[$t]['consumo_energia'] = 0;
        //                     //         $balance2[$t]['generacion_energia'] = 0;
        //                     //         $balance2[$t]['balance_neto'] = 0;
        //                     //     }
        //                     // }                        
        //                 }
        //             }else{
        //                 $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Hora')->get();

        //                 foreach ($consumo_diario_energia as $consu) {
        //                     $eje[] = $consu->eje;
        //                     $consumo_activa[] = $consu->activa;
        //                     if($consu->activa >= $max_consumo_activa)
        //                         $max_consumo_activa = $consu->activa;
        //                     $consumo_inductiva[] = $consu->inductiva;
        //                     $consumo_capacitiva[] = $consu->capacitiva;                    
        //                 }

        //                 $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->get();

        //                 $t = 0;
        //                 foreach ($balance as $val) 
        //                 {
        //                     if(isset($val->eje))
        //                     {
        //                         $balance2[$t]['eje'] = $val->eje;
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         // break;
        //                     }else{
        //                         $balance2[$t]['eje'] = $t+1;
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                     $t++;
        //                 }
        //             }
        //         }else{

        //         }
        //     break;

        //     default:
        //         $date_from = \Carbon\Carbon::now()->toDateString();
        //         $date_to = $date_from;
        //         if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
        //         {
        //             $date_from = Session::get('_flash')['date_from_personalice'];
        //             $date_to = Session::get('_flash')['date_to_personalice'];
        //         }
        //         $label_intervalo = 'Hoy';

        //         // if($tipo_count == 1)
        //         // {
        //             $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Hora')->get();


        //             foreach ($consumo_diario_energia as $consu) {
        //                 $eje[] = $consu->eje;
        //                 $consumo_activa[] = $consu->activa;
        //                 if($consu->activa >= $max_consumo_activa)
        //                     $max_consumo_activa = $consu->activa;
        //                 $consumo_inductiva[] = $consu->inductiva;
        //                 $consumo_capacitiva[] = $consu->capacitiva;                    
        //             }

        //         // }else{

        //             $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date',$date_from)->groupBy('time')->get();
        //             for ($t=0; $t < 1; $t++)
        //             {
        //                 foreach ($balance as $val) 
        //                 {
        //                     if(isset($val->eje))
        //                     {
        //                         $balance2[$t]['consumo_energia'] = $val->consumo_energia;
        //                         $balance2[$t]['generacion_energia'] = $val->generacion_energia;
        //                         $balance2[$t]['balance_neto'] = $val->balance_neto;
        //                         // break;
        //                     }else{
        //                         $balance2[$t]['consumo_energia'] = 0;
        //                         $balance2[$t]['generacion_energia'] = 0;
        //                         $balance2[$t]['balance_neto'] = 0;
        //                     }
        //                 }
        //             }
        //         // }

        //     break;
        //     }

        //     $user = Auth::user();//usuario logeado
        //     $titulo = 'Resumen Diario';//Título del content

        //     // dd($date_from, $date_to);

        //     // dd($balance2, $balance);
        //     // SE OBTIENEN LOS PERÍODOS DEL MES DE ACUERDO A LA TARIFA
        //     // $db_periodos = \DB::select("SELECT 3s.tarifa.Periodo as periodos FROM 3s.tarifa WHERE 3s.tarifa.Mes IN (SELECT MAX(MONTH(3s.datos_contador.date)) FROM 3s.datos_contador WHERE 3s.datos_contador.date >= '".$date_from."' AND 3s.datos_contador.date <='".$date_to."' )");
        //     // dd(\Carbon\Carbon::now()->format('l')[0]);


        //     $periodos2 = array();
        //     $db_EAct = array();
        //     $EAct = array();
        //     $db_p_contratada = array();
        //     $p_contratada = array();
        //     $periodos_coste = array();

        //     // SE OBTIENEN TODA LA POTENCIA DEMANDADA PARA EL INTERVALO SELECCIONADO
        //     // $db_p_demandada = \DB::select("SELECT 3s.datos_contador.`EAct imp(kWh)` as EAct FROM 3s.datos_contador WHERE `date` >= '".$date_from."' AND date <= '".$date_to."'");


        //     // CONSUMO DIARIO PARA EL INTERVALO SELECCIONADO

        //     // $consumo_diario_energia = \DB::select("SELECT `Hora`, SUM(`Energia Activa (kWh)`) as activa, SUM(`Energia Reactiva Inductiva (kVArh)`) as inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) as capacitiva FROM ".$contador.".consumo_diario_energia WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Hora");

        //     // dd($consumo_diario_energia);

        //     // SE CREAN ARRAYS CON LOS PERÍODOS DISPONIBLES EN LA COMPAÑÍA
        //     $aux_periodos = $db->table('Potencia_Contratada')->select(\DB::raw("COUNT(*) cont"))->groupBy('Periodo')->get()->toArray();
        //     for ($i=1; $i <= count($aux_periodos) ; $i++) {
        //         $periodos2[] = 'P'.$i;
        //         $periodos_coste[] = 'P'.$i;
        //     }
        //     // SE UNE AL ARRAY DE PERÍODOS DE LA COMPAÑÍA, LA OPCIÓN DE TOTAL
        //     array_push($periodos_coste, "Total"); 

        //     $MES = $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();        
        //     $total = 0;

        //     if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //     {
        //         if(!empty($MES))
        //         {
        //             $k = 0;
        //             foreach ($MES as $mes) {            
        //                 foreach ($periodos2 as $p) {
        //                     // SELECCIONA LA ENERGÍA ACTIVA MÁXIMA CONSUMIDA EN EL PERÍODO SELECCIONADO
        //                     if($tipo_count == 1)
        //                     {
        //                         $db_EAct[] = $db->table('Datos_Contador')->select(\DB::raw("MAX(`EAct imp(kWh)`)*4 as prom"))->join('Tarifa',"Datos_Contador.time",">=",\DB::raw("Tarifa.hora_start AND Tarifa.Mes = ".$mes->MES." AND Datos_Contador.time < Tarifa.hora_end"))->where("Datos_Contador.date", '>=',$date_from)->where("Datos_Contador.date", '<=',$date_to)->where("Tarifa.Periodo",$p)->where(\DB::raw('MONTH(Datos_Contador.date)'),$mes->MES)->get()->toArray();
        //                         if(is_null($db_EAct[$k][0]->prom))
        //                         {
        //                             $db_EAct[$k][0]->prom = 0;                
        //                         }

        //                     }

        //                     // // CALCULA POR PERÍODOS Y DEPENDIENDO DEL INTERVALO, LA CANTIDAD DE COSTO QUE SE INCURRE EN POTENCIA
        //                     // $db_coste_potencia[] = $db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(`Coste Termino Potencia (€)`) AS coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->where(\DB::raw('MONTH(date)'),$mes->MES)->where('Periodo',$p)->get()->toArray();
        //                     // // CALCULA EL COSTE DE ENERGÍA PARA EL INTERVALO DE FECHAS
        //                     // // INDICADO
        //                     // $db_coste_termino_energia[] = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(`Coste Termino Energia (€)`) as cost"))->where('date','>=',$date_from)->where('date','<=',$date_to)->where(\DB::raw('MONTH(date)'),$mes->MES)->where('Periodo',$p)->get()->toArray();


        //                     // // $precio_periodo[] = \DB::select("SELECT ".$contador.".precio_potencia.Precio, ".$contador.".precio_potencia.Periodo FROM ".$contador.".precio_potencia WHERE ".$contador.".precio_potencia.Periodo = '".$p."'");
        //                     // $db_p_contratada[] = $db->table('Potencia_Contratada')->select(\DB::raw("Potencia_contratada p_contratada"))->where('Periodo',$p)->get()->toArray();
        //                     $k++;
        //                 }            
        //             }
        //         }
        //     }else{
        //         $db_EAct[] = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("MAX(`Potencia Demandada (kW)`) as prom"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
        //     } 
        //     //$db_coste_potencia[] = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(`Coste Potencia Contratada (€)`) AS coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

        //     if($tipo_count < 3)
        //     {
        //         $index = 0;
        //         if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //         {
        //             $db_coste_potencia = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
        //             foreach ($db_coste_potencia as $coste_poten) {
        //                 $aux_index = 'costeP';
        //                 $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->costeP1;
        //                 $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->costeP2;
        //                 $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->costeP3;
        //                 $aux_coste_potencia[$index][$aux_index.($index+4)] = $coste_poten->costeP4;
        //                 $aux_coste_potencia[$index][$aux_index.($index+5)] = $coste_poten->costeP5;
        //                 $aux_coste_potencia[$index][$aux_index.($index+6)] = $coste_poten->costeP6;
        //                 $index++;
        //             }
        //         }else{
        //             $db_coste_potencia = $db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->get()->toArray();
        //             foreach ($db_coste_potencia as $coste_poten) {
        //                 $aux_index = 'costeP';
        //                 $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->costeP1;
        //                 $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->costeP2;
        //                 $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->costeP3;
        //                 $index++;
        //             }
        //             // dd($db_coste_potencia, $aux_coste_potencia);
        //         }
        //         //$db_excesos[] = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(`Coste Exceso Potencia (€)`) AS coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
        //         //$db_coste_termino_energia[] = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(`Coste Termino Energia (€)`) as cost"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

        //         $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
        //         $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
        //         $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
        //         $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
        //         $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
        //         $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
        //         $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();

        //         $index = 0;
        //         if($contador2->database == 'Prueba_Contador_6.0_V3' && ($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //         {
        //             $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

        //             $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(($kiP1 * $ktep) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(($kiP2 * $ktep) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(($kiP3 * $ktep) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(($kiP4 * $ktep) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(($kiP5 * $ktep) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(($kiP6 * $ktep) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

        //             foreach ($db_excesos as $excesos) {
        //                 $aux_index = 'costeP';
        //                 $aux_excesos[$index][$aux_index.($index+1)] = $excesos->costeP1;
        //                 $aux_excesos[$index][$aux_index.($index+2)] = $excesos->costeP2;
        //                 $aux_excesos[$index][$aux_index.($index+3)] = $excesos->costeP3;
        //                 $aux_excesos[$index][$aux_index.($index+4)] = $excesos->costeP4;
        //                 $aux_excesos[$index][$aux_index.($index+5)] = $excesos->costeP5;
        //                 $aux_excesos[$index][$aux_index.($index+6)] = $excesos->costeP6;
        //                 $index++;
        //             }

        //             // dd($db_excesos2, $db_excesos);
        //         }elseif(($contador2->tarifa != 2 && $contador2->tarifa != 3)){

        //             // 06 Jun 2020 - Alejandro Rivas
        //             // Se cambio para considerar el ciclo total, ya que la vista Coste Exceso Potencia, tiene una falla en el calculo para diferentes periodos
        //             // $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
        //             $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
        //             foreach ($db_excesos as $excesos) {
        //                 $aux_index = 'costeP';
        //                 $aux_excesos[$index][$aux_index.($index+1)] = $excesos->costeP1;
        //                 $aux_excesos[$index][$aux_index.($index+2)] = $excesos->costeP2;
        //                 $aux_excesos[$index][$aux_index.($index+3)] = $excesos->costeP3;
        //                 $aux_excesos[$index][$aux_index.($index+4)] = $excesos->costeP4;
        //                 $aux_excesos[$index][$aux_index.($index+5)] = $excesos->costeP5;
        //                 $aux_excesos[$index][$aux_index.($index+6)] = $excesos->costeP6;
        //                 $index++;
        //             }

        //             // $db_excesos2 = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

        //         }                            
        //         $index = 0;
        //         if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //         {
        //             $db_coste_termino_energia = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();


        //             foreach ($db_coste_termino_energia as $energia) 
        //             {
        //                 $aux_index = 'costeP';
        //                 $aux_energia[$index][$aux_index.($index+1)] = $energia->costeP1;
        //                 $aux_energia[$index][$aux_index.($index+2)] = $energia->costeP2;
        //                 $aux_energia[$index][$aux_index.($index+3)] = $energia->costeP3;
        //                 $aux_energia[$index][$aux_index.($index+4)] = $energia->costeP4;
        //                 $aux_energia[$index][$aux_index.($index+5)] = $energia->costeP5;
        //                 $aux_energia[$index][$aux_index.($index+6)] = $energia->costeP6;
        //                 $index++;
        //             }
        //         }else{

        //             $db_coste_termino_energia = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

        //             foreach ($db_coste_termino_energia as $energia) 
        //             {
        //                 $aux_index = 'costeP';
        //                 $aux_energia[$index][$aux_index.($index+1)] = $energia->costeP1;
        //                 $aux_energia[$index][$aux_index.($index+2)] = $energia->costeP2;
        //                 $aux_energia[$index][$aux_index.($index+3)] = $energia->costeP3;
        //                 $index++;
        //             }
        //         }

        //         $index = 0;                           


        //         //dd($db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get());
        //         //dd($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first());

        //         if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first())
        //         {
        //             $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get();
        //         }else{
        //             $potencia_optima[0]['p_optima'] =0;
        //             $potencia_optima[1]['p_optima'] =0;
        //             $potencia_optima[2]['p_optima'] =0;
        //             $potencia_optima[3]['p_optima'] =0;
        //             $potencia_optima[4]['p_optima'] =0;
        //             $potencia_optima[5]['p_optima'] =0;
        //         }
        //         // dd($potencia_optima);


        //         $db_p_contratada[] = $db->table('Potencia_Contratada')->select(\DB::raw("Potencia_contratada p_contratada"))->groupBy('Periodo')->get()->toArray();

        //         // INICIALIZA EL VECTOR DONDE SE ALMACENARÁ EL COSTO DE POTENCIA
        //         // DE ACUERDO AL INTERVALO SELECCIONADO
        //         $flag_aux = 0;
        //         for ($i=0; $i < count($aux_periodos); $i++) {
        //             $coste_potencia[] = 0;
        //             $coste_termino_energia[] = 0;
        //             $aux[] = 0;
        //         }
        //         // dd($db_EAct);
        //         if($tipo_count == 1)
        //         {
        //             if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //             {
        //                 for ($i=0; $i < count($MES) ; $i++)
        //                 {
        //                     for ($j=0; $j < count($aux_periodos); $j++)
        //                     {
        //                         if($aux[$j] <= $db_EAct[$j+($i*count($aux_periodos))])
        //                         {
        //                             $aux[$j] = $db_EAct[$j+($i*count($aux_periodos))];
        //                             $flag_aux = 1;
        //                         }
        //                     }
        //                 }
        //             }else{
        //                 // dd($db_EAct);
        //                 for ($j=0; $j < count($aux_periodos); $j++)
        //                 {
        //                     if($aux[$j] <= $db_EAct[0][$j]->prom)
        //                     {
        //                         $aux[$j] = $db_EAct[0][$j];
        //                         $flag_aux = 1;
        //                     }
        //                 }
        //             }                                
        //         }
        //         $P = array();
        //         // ALMACENA LA SUMATORIA DE LOS COSTOS DE POTENCIA DE ACUERDO A CADA
        //         // PERÍODO DENTRO DEL INTERVALO SELECCIONADO
        //         $total = 0;
        //         $total2 = 0;
        //         // dd($db_coste_potencia, $db_excesos);
        //         $i = 0;
        //         for ($i=0; $i < count($aux_periodos); $i++)
        //         {
        //             $aux_index = 'costeP'.($i+1);
        //             if(!empty($db_coste_potencia) && (isset($aux_coste_potencia[0][$aux_index]) && isset($aux_excesos[0][$aux_index])) )
        //             {
        //                 $coste_potencia[$i%count($aux_periodos)] = $coste_potencia[$i%count($aux_periodos)] + $aux_coste_potencia[0][$aux_index] + $aux_excesos[0][$aux_index];
        //                 $total = $aux_coste_potencia[0][$aux_index] + $total + $aux_excesos[0][$aux_index];
        //             }elseif(!empty($db_coste_potencia) && isset($aux_coste_potencia[0][$aux_index])){
        //                 $coste_potencia[$i%count($aux_periodos)] = $coste_potencia[$i%count($aux_periodos)] + $aux_coste_potencia[0][$aux_index];
        //                 $total = $aux_coste_potencia[0][$aux_index] + $total;
        //             }else{
        //                 $coste_potencia[$i] = 0;
        //                 $total = 0;
        //             }

        //             if(!empty($aux_energia) && isset($aux_energia[0][$aux_index]))
        //             {
        //                 $coste_termino_energia[$i%count($aux_periodos)] = $coste_termino_energia[$i%count($aux_periodos)] + $aux_energia[0][$aux_index];
        //                 $total2 = $aux_energia[0][$aux_index] + $total2;
        //             }else{
        //                 $coste_termino_energia[$i] = 0;
        //                 $total2 = 0;
        //             }
        //             if(!empty($db_p_contratada) && isset($db_p_contratada[0][$i]))
        //             {
        //                 $p_contratada[] = $db_p_contratada[0][$i]->p_contratada;
        //             }else{
        //                 $p_contratada[$i] = 0;
        //             }
        //         }
        //         array_push($coste_potencia, $total);
        //         array_push($coste_termino_energia, $total2);
        //         // dd($coste_potencia);
        //         $j = 0;
        //         if($flag_aux == 1)
        //         {
        //             if($tipo_count == 1)
        //             {
        //                 if(($contador2->tarifa != 2 && $contador2->tarifa != 3))
        //                 {
        //                     foreach ($aux as $prom_EAct) {
        //                         if(is_null($prom_EAct[0]->prom))
        //                         {
        //                             $EAct[] = 0;
        //                             continue;
        //                         }else{
        //                             $EAct[$j] = $prom_EAct[0]->prom;
        //                         }
        //                         $i++;
        //                         $j++;
        //                     }
        //                 }else{
        //                     foreach ($aux as $prom_EAct) {
        //                         if(is_null($prom_EAct))
        //                         {
        //                             $EAct[] = 0;
        //                             continue;
        //                         }else{
        //                             $EAct[$j] = $prom_EAct->prom;
        //                         }
        //                         $i++;
        //                         $j++;
        //                     }
        //                 }
        //             }

        //         }else{
        //             for ($i=0; $i < count($aux_periodos); $i++)
        //             {
        //                 $EAct[] = 0;
        //             }
        //         }
        //         // dd('eac',$EAct);
        //         // ******************************************************************
        //         // Calculo de la gráfica para Energía consumida Activa y Reactiva
        //         // ******************************************************************


        //         $Energia_Act_Reac_Consu = array();
        //         $db_Ener_Consu_Acti_Reacti[] = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("`Periodo`,SUM(`Energia Activa (kWh)`) E_Activa, SUM(`Energia Reactiva Inductiva (kVArh)`) E_Reac_Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) E_Reac_Cap"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
        //         $energia_activa_max = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("(SUM(`Energia Activa (kWh)`)) max_Activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->orderBy('max_Activa','DESC')->first();
        //         if(!is_null($energia_activa_max))
        //         {
        //             $energia_activa_max = $energia_activa_max->max_Activa;
        //         }
        //         else
        //             $energia_activa_max = 0;

        //         $db_Venta_Energia = array();
        //         $total_ventas = 0;
        //         if($contador2->tipo == 2)
        //         {
        //             $db_Venta_Energia = $db->table('Venta_Energia_Activa')->select(\DB::raw("SUM(P1) ventaP1, SUM(P2) ventaP2, SUM(P3) ventaP3, SUM(P4) ventaP4, SUM(P5) ventaP5, SUM(P6) ventaP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
        //             // dd($db_Venta_Energia);
        //             $index = 0;
        //             $aux_index = 'ventaP';
        //             foreach ($db_Venta_Energia as $venta) {
        //                 $aux_Venta_Energia[$index][$aux_index.($index+1)] = $venta->ventaP1;
        //                 $aux_Venta_Energia[$index][$aux_index.($index+2)] = $venta->ventaP2;
        //                 $aux_Venta_Energia[$index][$aux_index.($index+3)] = $venta->ventaP3;
        //                 $aux_Venta_Energia[$index][$aux_index.($index+4)] = $venta->ventaP4;
        //                 $aux_Venta_Energia[$index][$aux_index.($index+5)] = $venta->ventaP5;
        //                 $aux_Venta_Energia[$index][$aux_index.($index+6)] = $venta->ventaP6;
        //                 $index++;
        //             }

        //             $generacion = $db->table('Generacion_Energia_Activa_y_Reactiva')->select(\DB::raw("Periodo,SUM(`Generación Energia Activa (kWh)`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

        //             $h = 0;
        //             if(!empty($db_Venta_Energia))
        //             {
        //                 foreach ($aux_Venta_Energia[0] as $ventas) {
        //                     $total_ventas = $total_ventas + $ventas;
        //                     $h++;
        //                 }
        //             }
        //         }

        //         if(empty($db_Ener_Consu_Acti_Reacti[0])){
        //             for ($i=0; $i < count($aux_periodos); $i++) {
        //                 $Energia_Act[$i] = 0;
        //                 $Energia_Reac_Induc[$i] = 0;
        //                 $Energia_Reac_Cap[$i] = 0;
        //             }
        //         }else
        //         {
        //             foreach ($db_Ener_Consu_Acti_Reacti[0] as $it) {
        //                 $Energia_Act[] = $it->E_Activa;
        //                 $Energia_Reac_Induc[] = $it->E_Reac_Induc;
        //                 $Energia_Reac_Cap[] = $it->E_Reac_Cap;
        //             }
        //         }
        //     }

        //     // $db_coste_termino_energia = array();
        //     $contador_label = $contador2->count_label;

        //     // PARA GAS
        //     if($tipo_count == 3)
        //     {
        //         $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) coste_fijo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first()->coste_fijo;
        //         $coste_termino_variable = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) coste_variable"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first()->coste_variable;
        //         if(isset($db->table('Caudal_diario_contratado')->select(\DB::raw("`Caudal_diario_contratado` QD"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->QD))
        //             $QD_contratado = $db->table('Caudal_diario_contratado')->select(\DB::raw("`Caudal_diario_contratado` QD"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->QD;
        //         else
        //             $QD_contratado = 0;
        //         if(isset($db->table('Poder_calorifico_superior')->select(\DB::raw("PCS"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->PCS))
        //             $PCS = $db->table('Poder_calorifico_superior')->select(\DB::raw("PCS"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first()->PCS;
        //         else
        //             $PCS = 0;

        //         $tarifa = $db->table('Area_Cliente')->select(\DB::raw("`TARIFA` tarifa"))->first()->tarifa;
        //     }
        //     if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
        //         $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        //     else
        //         $dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();

        //     $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".Auth::user()->id);

        //     if(is_null($aux_current_count) || empty($aux_current_count))
        //         \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$contador2->count_label."',".Auth::user()->id.")");
        //     else
        //         \DB::update("UPDATE current_count SET label_current_count = '".$contador2->count_label."' WHERE user_id = ".$id);

        //     \DB::disconnect('mysql2');

        //     $peri = count($aux_periodos);

        //     //return view('resumen_energia_potencia.resumen_energia_potencia',compact('user'));
        //     return view('resumen_energia_potencia.resumen_energia_potencia',compact('user','titulo','cliente','id','ctrl','periodos2','EAct','p_contratada','periodos_coste','coste_potencia','array_total','Energia_Act','Energia_Reac_Cap','Energia_Reac_Induc', 'coste_termino_energia', 'consumo_diario_energia','label_intervalo','eje','consumo_activa','consumo_capacitiva','consumo_inductiva','date_from','date_to','tipo_count','db_Venta_Energia','total_ventas','balance','generacion','contador_label','domicilio','potencia_optima','dir_image_count','interval','energia_activa_max','max_consumo_activa','balance2','tipo_tarifa','peri'));
        // }else{
        //     $titulo = 'Resumen de Contadores';
        //     $tipo_count = 0;
        //     return view('Dashboard.dashboard',compact('user','titulo','tipo_count','tipo_tarifa'))
        //     ->with( 'maps_url', '' )
        //         ->with( 'markers', null );

        // }

    }

    public function ReinicioPassword()
    {
        return view('reinicio.reinicio_password');
    }
}
