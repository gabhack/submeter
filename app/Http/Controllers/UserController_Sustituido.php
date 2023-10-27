<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Swift_SmtpTransport;
use Swift_Mailer;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Mail\CreateClienteMail as CreateClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\PerfilUserRequest;
use App\Jobs\SendEmailJob;
use App\Http\Requests\UsuarioRegistradoRequest;
use App\Mail\OptimizationRequest;
use App\User;
use App\Informes;
use App\Informes_analizadores;
use App\analyzer_alertas_informes;
use App\Alertas;
use App\User2;
use App\Perfil;
use App\Count;
use App\CurrentCount;
use App\Analizador;
use App\intervalos_user;
use App\Groups;
use Session;
use Validator;
use Auth;
use File;
use PDF;
use Response;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\UserEnergyMeters;
use App\Enterprise;
use App\EnergyMeter;
use App\EnterpriseUser;
use App\EnterpriseEnergyMeter;
use App\AnalyzerMeter;
use App\UserAnalyzers;

class UserController extends Controller
{
    protected $intervalo;

    //Solo Usuario Logedo
    public function __construct()
    {
        $this->middleware('auth');
    }

    function AdministrarUsuarios($id, $id_delete_intervals,Request $request)
    {
        // La función recibe el tipo de usuario que se desea administrar, si es 1, edita o crea administrador. Si es 2, edita o crea cliente


        $tipo_count = 0;
        $user = Auth::user();
        //Users representa todos los usuarios clientes y user representa el usuario logeado
        $titulo = 'Administración General';
        // dd("DELETE FROM intervalos_users WHERE user_id = ".$id_delete_intervals);
        \DB::delete("DELETE FROM intervalos_users WHERE user_id = ".$id_delete_intervals." AND ctrl = 0");
        $vacio = '';
        $sesion = $request->session()->all();
        $flash = $sesion['_flash'];
        $flash['current_count'] = $vacio;
        Session::put('_flash',$flash);
        $url = Session::get('_previous')['url'];


        if($id == 1 && Auth::user()->tipo == 1)
        {
            $users = User::where('tipo',1)->get();
            //dd($users);
            return view('User.admin_users',compact('user','users','id','titulo','tipo_count'));
        }elseif(Auth::user()->tipo == 1){

            $users = User::where('tipo',2)->get();
            //dd($users);
            return view('User.admin_users',compact('user','users','id','tipo_count'));
        }else{
            return \Redirect::to('https://submeter.es/');
        }


    }

    function VerPanelUser($id,$ctrl)
    {
        // id representa el id del usuario que se desea ver  y $ctrl el control que indica que
        // la vista mostrada viene del panel administrativo

        $user = User::where('id',$id)->get()->first();
        $contador = (request()->input('contador'));
        $tipo_count = (request()->input('tipo'));
        $interval = Session::get('_flash')['intervalos'];

        if(empty($tipo_count))
        {
            $tipo_count = Count::where('user_id',$id)->first()->tipo;
            $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

        }
        $array_coste_activa = array();
        $coste_activa = array();
        $array_coste_reactiva = array();
        $coste_reactiva = array();
        $array_potencia_contratada = array();
        $potencia_contratada = array();
        $array_exceso_potencia = array();
        $exceso_potencia = array();
        $array_impuesto = array();
        $impuesto = array();
        $array_equipo = array();
        $equipo = array();
        $equipo_medida = array();
        $termino_fijo = array();
        $termino_variable = array();
        $consumo_GN_kWh = array();
        $I_E_HC = array();
        $iee = array();

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        $aux_current_count = $aux_current_count[0]->label_current_count;
        // dd($contador, $aux_current_count, Session::get('_flash')['current_count']);
        if(!is_null($aux_current_count) || !empty($aux_current_count))
        {
            if(isset(Session::get('_flash')['current_count']))
            {
                if(Session::get('_flash')['current_count'] != $aux_current_count)
                {
                    $flash['current_count'] = $aux_current_count;
                    $flash['intervalos'] = $interval;
                    Session::put('_flash',$flash);
                }
            }

        }

        if(!isset(Session::get('_flash')['current_count']))
        {
            if(empty($contador))
            {
                // $contador2 = Count::where('user_id',$id)->first();
                // $sesion = $request->session()->all();
                // $flash = $sesion['_flash'];
                // $flash['current_count'] = $contador2->count_label;
                // Session::put('_flash',$flash);
                // $url = Session::get('_previous')['url'];

                $tipo_count = strtolower(request()->input('tipo'));
                if(empty($tipo_count))
                {
                    $tipo_count = Count::where('user_id',$id)->first()->tipo;
                    $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;
                }

            }else{
                // $contador2 = Count::where('count_label',$contador)->first();
                // $sesion = $request->session()->all();
                // $flash = $sesion['_flash'];
                // $flash['current_count'] = $contador2->count_label;
                // Session::put('_flash',$flash);
                // $url = Session::get('_previous')['url'];

                $tipo_count = strtolower(request()->input('tipo'));
                if(empty($tipo_count))
                {
                    $tipo_count = Count::where('user_id',$id)->first()->tipo;
                    $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;
                }
            }
        }else{
            $current_count = Session::get('_flash')['current_count'];
            if(empty($contador))
            {
                // $contador2 = Count::where('count_label',$current_count)->first();
                // $sesion = $request->session()->all();
                // $flash = $sesion['_flash'];
                // $flash['current_count'] = $contador2->count_label;
                // Session::put('_flash',$flash);
                // $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = Count::where('count_label',$current_count)->first()->tipo;
                $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

            }else{
                // $contador2 = Count::where('count_label',$contador)->first();
                // $sesion = $request->session()->all();
                // $flash = $sesion['_flash'];
                // $flash['current_count'] = $contador2->count_label;
                // Session::put('_flash',$flash);
                // $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = Count::where('count_label',$current_count)->first()->tipo;
                $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;
            }
        }

        if($tipo_count < 3)
            $contador2 = Count::where('user_id',$id)->where('tipo',1)->get()->toArray();
        else
            $contador2 = Count::where('user_id',$id)->where('tipo',3)->get()->toArray();
            // dd($contador2);

        $interval = Session::get('_flash')['intervalos'];
        // dd($interval);
        $titulo = 'Resumen de Contadores';
        $hoy = \Carbon\Carbon::now();


        switch ($interval) {
            case '2':
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }

                $label_intervalo = 'Hoy';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }

                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                if($dont == 0)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }
                }elseif($now == 4 || $now == 7 || $now == 10){
                    // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    }
                }elseif($now == 5 || $now == 8 || $now == 11){
                    // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    }
                }elseif($now == 6 || $now == 9 || $now == 12){
                    // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Último Trimestre';
                break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
            break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
                break;

            default:
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                break;
        }
        $i = 0;
        $j = 0;
        $fechaEmision = \Carbon\Carbon::parse($date_from);
        $fechaExpiracion = \Carbon\Carbon::parse($date_to);

        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);

        $countsArray = Count::where('user_id',$id)->get()->toArray();

        foreach ($countsArray as $cont) {

            config(['database.connections.mysql2.host' => $cont['host']]);
            config(['database.connections.mysql2.port' => $cont['port']]);
            config(['database.connections.mysql2.database' => $cont['database']]);
            config(['database.connections.mysql2.username' => $cont['username']]);
            config(['database.connections.mysql2.password' => $cont['password']]);
            env('MYSQL2_HOST',$cont['host']);
            env('MYSQL2_DATABASE',$cont['database']);
            env('MYSQL2_USERNAME', $cont['username']);
            env('MYSQL2_PASSWORD',$cont['password']);

            $db = \DB::connection('mysql2');

            $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();
            // dd($cont['database']);
            // dd($tipo_count);

            if($cont['tipo'] < 3 && $cont['tarifa'] == 1)
            {
                $db_coste_activa = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $index = 0;
                foreach ($db_coste_activa as $coste_ac) {
                    $aux_index = 'costeP';
                    $aux_coste_activa[$index][$aux_index.($index+1)] = $coste_ac->costeP1;
                    $aux_coste_activa[$index][$aux_index.($index+2)] = $coste_ac->costeP2;
                    $aux_coste_activa[$index][$aux_index.($index+3)] = $coste_ac->costeP3;
                    $aux_coste_activa[$index][$aux_index.($index+4)] = $coste_ac->costeP4;
                    $aux_coste_activa[$index][$aux_index.($index+5)] = $coste_ac->costeP5;
                    $aux_coste_activa[$index][$aux_index.($index+6)] = $coste_ac->costeP6;
                    $index++;
                }
                // dd($db_coste_activa);

                $coste_activa[] = $aux_coste_activa;


                // $coste_activa = floatval(\DB::select("SELECT SUM(`Coste Energia Activa (€)`) valor FROM ".$contador.".coste_energia_activa WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                    $aux_coste_reactiva[$index][$aux_index.($index+4)] = $coste_reac->costeP4;
                    $aux_coste_reactiva[$index][$aux_index.($index+5)] = $coste_reac->costeP5;
                    $aux_coste_reactiva[$index][$aux_index.($index+6)] = $coste_reac->costeP6;
                    $index++;
                }
                $coste_reactiva[] = $aux_coste_reactiva;

                // $coste_reactiva = floatval(\DB::select("SELECT SUM(`Coste Energia Reactiva (€)`) valor FROM ".$contador.".coste_energia_reactiva WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);

                $db_potencia_contratada = ($db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;
                foreach ($db_potencia_contratada as $poten_contra) {
                    $aux_index = 'costeP';
                    $aux_potencia_contratada[$index][$aux_index.($index+1)] = $poten_contra->costeP1;
                    $aux_potencia_contratada[$index][$aux_index.($index+2)] = $poten_contra->costeP2;
                    $aux_potencia_contratada[$index][$aux_index.($index+3)] = $poten_contra->costeP3;
                    $aux_potencia_contratada[$index][$aux_index.($index+4)] = $poten_contra->costeP4;
                    $aux_potencia_contratada[$index][$aux_index.($index+5)] = $poten_contra->costeP5;
                    $aux_potencia_contratada[$index][$aux_index.($index+6)] = $poten_contra->costeP6;
                    $index++;
                }
                $potencia_contratada[] = $aux_potencia_contratada;

                // $potencia_contratada = floatval(\DB::select("SELECT SUM(`Coste Potencia Contratada (€)`) valor FROM ".$contador.".coste_potencia_contratada WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);

                if($cont['database'] == 'Prueba_Contador_6.0_V3')
                {
                    $db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();
                }else{

                    $db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                }

                    // $db_exceso_potencia = ($db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;
                foreach ($db_exceso_potencia as $exceso_poten) {
                    $aux_index = 'costeP';
                    $aux_exceso_potencia[$index][$aux_index.($index+1)] = $exceso_poten->costeP1;
                    $aux_exceso_potencia[$index][$aux_index.($index+2)] = $exceso_poten->costeP2;
                    $aux_exceso_potencia[$index][$aux_index.($index+3)] = $exceso_poten->costeP3;
                    $aux_exceso_potencia[$index][$aux_index.($index+4)] = $exceso_poten->costeP4;
                    $aux_exceso_potencia[$index][$aux_index.($index+5)] = $exceso_poten->costeP5;
                    $aux_exceso_potencia[$index][$aux_index.($index+6)] = $exceso_poten->costeP6;
                    $index++;
                }
                $exceso_potencia[] = $aux_exceso_potencia;
                // dd($aux_exceso_potencia);

                // $exceso_potencia = floatval(\DB::select("SELECT SUM(`Coste Exceso Potencia (€)`) valor FROM ".$contador.".coste_exceso_potencia WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                $aux_impuesto = 0;
                $index = 0;
                // if($i == 4)
                //     dd($aux_coste_activa, $coste_reactiva, $potencia_contratada, $exceso_potencia);
                // $iee[] = $cont['iee'];
                // dd($iee);
                if($cont['iee'] == 3)
                {
                    $aux_iee = 0;
                }elseif($cont['iee'] == 2){
                    $aux_iee = 0.15;
                }else{
                    $aux_iee = 1;
                }

                foreach ($aux_coste_activa[0] as $coste) {
                    $aux_index = 'costeP';

                    $aux_impuesto = $aux_impuesto+(($coste + $coste_reactiva[$i][0][$aux_index.($index+1)] + $potencia_contratada[$i][0][$aux_index.($index+1)] + $aux_exceso_potencia[0][$aux_index.($index+1)])*0.0511269632)*$aux_iee;

                    $index++;
                }
                if($cont['tipo'] == 1)
                {
                    $iee[] = $aux_impuesto;
                }
                // if($i == 4)
                //     dd($coste_reactiva[$i][0]['costeP4'] , $potencia_contratada[$i][0]['costeP4'] , $exceso_potencia[$j][0]['costeP4'],$j);

                $impuesto[] = $aux_impuesto;

                $equipo[] = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
                // dd($equipo[0][0]->valor*($diasDiferencia+1));
                $i++;
                $j++;
            }elseif($cont['tipo'] == 3 && $cont['tarifa'] != 2 && $cont['tarifa'] != 3){
                $termino_variable[] = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) valor"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $termino_fijo[] = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) valor"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $equipo_medida[] = $db->table('Equipo_de_medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

                $consumo_GN_kWh[] = $db->table('Consumo_GN_kWh')->select(\DB::raw("SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $I_E_HC[] = $db->table('Impuesto_HC')->select(\DB::raw("Impuesto_HC valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
            }elseif($cont['tipo'] < 3 && $cont['tarifa'] != 1){

                $db_coste_activa = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $index = 0;
                $aux_coste_activa = array();
                foreach ($db_coste_activa as $coste_ac) {
                    $aux_index = 'costeP';
                    $aux_coste_activa[$index][$aux_index.($index+1)] = $coste_ac->costeP1;
                    $aux_coste_activa[$index][$aux_index.($index+2)] = $coste_ac->costeP2;
                    $aux_coste_activa[$index][$aux_index.($index+3)] = $coste_ac->costeP3;

                    $index++;
                }

                $coste_activa[] = $aux_coste_activa;
                // dd($coste_activa);

                // $coste_activa = floatval(\DB::select("SELECT SUM(`Coste Energia Activa (€)`) valor FROM ".$contador.".coste_energia_activa WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;
                $aux_coste_reactiva = array();
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;

                    $index++;
                }
                $coste_reactiva[] = $aux_coste_reactiva;

                // $coste_reactiva = floatval(\DB::select("SELECT SUM(`Coste Energia Reactiva (€)`) valor FROM ".$contador.".coste_energia_reactiva WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);

                // $db_potencia_contratada = ($db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $db_potencia_contratada = ($db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('Max(`date`)','>=',$date_from)->where('Max(`date`)','<=',$date_to)->get()->toArray());

                $index = 0;
                $aux_potencia_contratada = array();
                foreach ($db_potencia_contratada as $poten_contra) {
                    $aux_index = 'costeP';
                    $aux_potencia_contratada[$index][$aux_index.($index+1)] = $poten_contra->costeP1;
                    $aux_potencia_contratada[$index][$aux_index.($index+2)] = $poten_contra->costeP2;
                    $aux_potencia_contratada[$index][$aux_index.($index+3)] = $poten_contra->costeP3;

                    $index++;
                }
                $potencia_contratada[] = $aux_potencia_contratada;
                // dd($potencia_contratada);
                // $potencia_contratada = \DB::select("select `".$cont['database']."`.`ZPI_Contador_Festivos_Periodos`.`date` AS `date`,max((case when (`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Periodo` = 'P1') then (((`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Precio`) * 12) / 365) else 0 end)) AS `P1`,max((case when (`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Periodo` = 'P2') then (((`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Precio`) * 12) / 365) else 0 end)) AS `P2`,max((case when (`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Periodo` = 'P3') then (((`".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Precio`) * 12) / 365) else 0 end)) AS `P3` from (`".$cont['database']."`.`ZPI_Contador_Festivos_Periodos` left join `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada` on(((`".$cont['database']."`.`ZPI_Contador_Festivos_Periodos`.`date` between `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Potencia Contratada desde` and `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Potencia Contrada hasta`) and (`".$cont['database']."`.`ZPI_Contador_Festivos_Periodos`.`date` between `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Precio potencia desde` and `".$cont['database']."`.`ZPI_Precio_Potencia_Contratada`.`Precio potencia hasta`)))) group by `".$cont['database']."`.`ZPI_Contador_Festivos_Periodos`.`date`");
                // dd($potencia_contratada);

                // $potencia_contratada = floatval(\DB::select("SELECT SUM(`Coste Potencia Contratada (€)`) valor FROM ".$contador.".coste_potencia_contratada WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);


                // $exceso_potencia = floatval(\DB::select("SELECT SUM(`Coste Exceso Potencia (€)`) valor FROM ".$contador.".coste_exceso_potencia WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                $aux_impuesto = 0;
                $index = 0;
                // $iee[] = $cont['iee'];
                // dd('otro',$iee);
                if($cont['iee'] == 3)
                {
                    $aux_iee = 0;
                }elseif($cont['iee'] == 2){
                    $aux_iee = 0.15;
                }else{
                    $aux_iee = 1;
                }

                foreach ($aux_coste_activa[0] as $coste) {
                    $aux_index = 'costeP';
                    if(isset($exceso_potencia[$i]))
                    {
                        $aux_impuesto = $aux_impuesto+(($coste + $coste_reactiva[$i][0][$aux_index.($index+1)] + $potencia_contratada[$i][0][$aux_index.($index+1)] + $exceso_potencia[$i][0][$aux_index.($index+1)])*0.0511269632)*$aux_iee;
                    }else{
                        $aux_impuesto = $aux_impuesto+(($coste + $coste_reactiva[$i][0][$aux_index.($index+1)] + $potencia_contratada[$i][0][$aux_index.($index+1)])*0.0511269632)*$aux_iee;
                    }
                    $index++;
                }

                if($cont['tipo'] == 1)
                {
                    $iee[] = $aux_impuesto;
                }

                $impuesto[] = $aux_impuesto;
                $equipo[] = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
                $aux_i = count($exceso_potencia);
                $exceso_potencia[$aux_i][0]['costeP1'] = 0;
                $exceso_potencia[$aux_i][0]['costeP2'] = 0;
                $exceso_potencia[$aux_i][0]['costeP3'] = 0;
                $exceso_potencia[$aux_i][0]['costeP4'] = 0;
                $exceso_potencia[$aux_i][0]['costeP5'] = 0;
                $exceso_potencia[$aux_i][0]['costeP6'] = 0;
                $i++;
                $j++;
            }

            if(!isset($dir_image_count))
            {
                if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
                    $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
                else
                    $dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
            }

            \DB::disconnect('mysql2');
        }
        $cont = $contador;
        // dd($consumo_GN_kWh[0],$consumo_GN_kWh[1]);
        // dd($termino_variable, $termino_fijo, $equipo_medida, $consumo_GN_kWh, $I_E_HC);
        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
                        // dd($aux_current_count);
        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count, user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$aux_current_count[0]->label_current_count."' WHERE user_id = ".$id);

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            if($tipo_count < 3)
                return view('Dashboard.dashboard',compact('user','ctrl','titulo','id','coste_activa', 'coste_reactiva', 'potencia_contratada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','label_intervalo','tipo_count','array_contadores','diasDiferencia','domicilio','dir_image_count','tipo_tarifa','iee'))
                ->with( 'maps_url', '' )
                    ->with( 'markers', null );
            else
                return view('Gas.contadores',compact('user','ctrl','titulo','id','hoy','date_from','date_to','cont','label_intervalo','tipo_count','array_contadores','termino_variable','termino_fijo','equipo_medida','consumo_GN_kWh','I_E_HC','diasDiferencia','domicilio','dir_image_count','tipo_tarifa','iee'));
        }
        return \Redirect::to('https://submeter.es/');

    }

    function EliminarUsuarioLista(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            // $id_product = \DB::select("select id from products where referencia = '".$data."'");
            \DB::delete('delete from users WHERE id = ? ',array($data['user_id']));
            // return ['data' => $request];
        }
    }

    /*
     * Función que retorna la vista para crear los usuarios de tipo administrador.
     */

    function create()
    {
        $user = Auth::user();
        $tipo_count = 0;
        if(Auth::user()->tipo == 1)
            return view('User.create', compact('user','tipo_count'));
        return \Redirect::to('https://submeter.es/');
    }

    /*
     * Función que retorna la vista para crear los usuarios clientes.
     */

    function createClient()
    {
        $user = Auth::user();
        $tipo_count = 0;

        if(Auth::user()->tipo == 1)
            return view('User.create_client', compact('user','tipo_count'));
        return \Redirect::to('https://submeter.es/');
    }

    private function clientValidate($request){
        $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => 'required|email|unique:users,email',
                        'tipo' => 'required',
                        'contadores' => 'required|numeric|digits_between:1,10|min:1',
                        'password' => 'required|min:6',
        ],[
                        'email.unique' => 'El campo correo ya existe',
                        'numeric' => 'El campo Contadores debe ser numérico',
                        'email' => 'El campo Correo debe ser de tipo email',
                        'required' => 'El campo es requerido',
                        'password.min' => 'La contraseña debe tener mínimo :min caracteres',
                        'contadores.min' => 'El campo Contadores debe ser mínimo :min',
                        'regex' => 'La contraseña debe contener al menos una mayúsculas, una minúscula, un número y alguno de estos caracteres (!, $, #, %, *).'
        ]);

        return $validator;
    }

    private function adminValidate($request){
        $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => 'required|email|unique:users,email',
                        'tipo' => 'required',
                        'password' => 'required|min:6',
        ],[
                        'email.unique' => 'El campo correo ya existe',
                        'email' => 'El campo Correo debe ser de tipo email',
                        'required' => 'El campo es requerido',
                        'password.min' => 'La contraseña debe tener mínimo :min caracteres',
                        'regex' => 'La contraseña debe contener al menos una mayúsculas, una minúscula, un número y alguno de estos caracteres (!, $, #, %, *).'
        ]);

        return $validator;
    }

    /*
     * Función para almacenar los usuarios.
     */
    function store(Request $request)
    {
        // dd($request);
        if($request->tipo == 2){
            $nombre = $request->name;
            $users = User::all();

            $aux = 0;
            foreach ($users as $usuario) {
                if ($usuario->email == $request->email) {
                    $aux = 1;
                }
            }

            $validate = $this->clientValidate($request);

            if ($validate->fails()) {
                $errors = $validate->messages();
                return redirect()->to('/registrar-cliente')->with(compact('errors'));
            }

            if($aux == 0)
            {
                $user = new User();

                $user->name = $nombre;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->tipo = $request->tipo;
                $user->save();

                $interval_user = new intervalos_user;
                $interval_user->id_carbon_interval = 1;
                $interval_user->user_id = $user->id;
                $interval_user->ctrl = 1;
                $interval_user->save();

                $cont = $request->contadores;

                for ($i=1; $i <= $cont; $i++) {
                    $contador = new Count();
                    $contador->count_label = $request['name_cont'.($i-1)];
                    $contador->user_id = $user->id;
                    $contador->host = $request['val_host'.($i-1)];
                    $contador->port = $request['val_port'.($i-1)];
                    $contador->database = $request['val_dbase'.($i-1)];
                    $contador->username = $request['val_username'.($i-1)];
                    $contador->password = $request['val_password'.($i-1)];
                    $contador->tipo = $request['tipo'.($i-1)];
                    $contador->save();

                    for ($j=0; $j < $request['analizadores_'.($i-1)]; $j++) {
                        $analizador = new Analizador();
                        $analizador->label = $request['name_analizador'.$j.'_contador_'.($i)];
                        $analizador->count_id = $contador->id;
                        $analizador->host = $request['val_host_analizador'.$j.'_contador_'.($i)];
                        $analizador->port = $request['val_port_analizador'.$j.'_contador_'.($i)];
                        $analizador->database = $request['val_dbase_analizador'.$j.'_contador_'.($i)];
                        $analizador->username = $request['val_username_analizador'.$j.'_contador_'.($i)];
                        $analizador->password = $request['val_password_analizador'.$j.'_contador_'.($i)];
                        if($j == 0)
                        {
                            $analizador->principal = 1;
                        }
                        else{
                            $analizador->principal = 0;
                        }
                        $analizador->save();
                    }
                }

                $codigo = $request->password;

                Mail::to($user->email, 'Submeter')
                ->send(new CreateClient($codigo,$user));

                Session::flash('message', 'El usuario y sus contadores han sido creados con éxito!.');

                return \Redirect::back();
            }else{
                Session::flash('message-error', "El usuario con la empresa ". $request->name ." ya existe!");

                return \Redirect::back();
            }

        }else{
            $validate = $this->adminValidate($request);


            if ($validate->fails()) {
                $errors = $validate->messages();
                return redirect()->to('/registrar')->with(compact('errors'));
            }

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->tipo = $request->tipo;
            $user->save();

            $codigo = $request->password;

            Mail::to($user->email, '3Seficiencia')
            ->send(new CreateClient($codigo));

            Session::flash('message', 'El usuario Administrador ha sido creado con éxito!.');

            return \Redirect::back();
        }
    }

    // ---------------------------------------------------
    // METODO ConfigurarIntervalo: se encarga de tomar el intervalo
    // seleccionado por el usuario y empilarlo en la BD en la tabla
    // de registros temporales "intervalos_users", para posteriormente
    // usar cada intervalo seleccionado y configurar todas las gráficas y
    // cálculos (la tabla eliminará los registros de un usuario específico cuando este termine sesión)
    // ---------------------------------------------------

    function ConfigurarIntervalo(Request $request)
    {
        $interval = $request->option_interval;
        $date_from_personalice = $request->date_from_personalice;
        $date_to_personalice = $request->date_to_personalice;
        if(strtotime($date_to_personalice) <= strtotime($date_from_personalice)){
          $date_to_personalice = $date_from_personalice;
        }
        $sesion = $request->session()->all();
        $flash = $sesion['_flash'];
        $flash['intervalos'] = $interval;
        $flash['date_from_personalice'] = $date_from_personalice;
        $flash['date_to_personalice'] = $date_to_personalice;
        $flash['current_date'] = $date_to_personalice;
        Session::put('_flash',$flash);
        if(isset(Session::get('_flash')['label_intervalo_navigation']))
        {
            $flash['label_intervalo_navigation'] = 'otra cosa';
            Session::put('_flash',$flash);
        }
        $url = Session::get('_previous')['url'];
        //$url = Session::flush();
        //var_dump($request->session()->all());
        //return $this->ResumenEnergiaPotencia($request->user_id);
        return \Redirect::back();
        //Session::flash('interval',$interval);
        //return redirect($url);
    }

    function ConfigurarIntervaloNavegacion(Request $request)
    {
        $interval = $request->option_interval;
        $current_date = "";
        switch ($request->label_intervalo) {
            case 'Ayer':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $interval = 1;
            break;

            case 'Semana Actual':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $interval = 3;
            break;

            case 'Semana Anterior':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $interval = 4;
            break;

            case 'Mes Actual':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $interval = 5;
            break;

            case 'Mes Anterior':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $interval = 6;
            break;

            case 'Ultimo Trimestre':
                $now = \Carbon\Carbon::now()->month;
                $interval = 7;
                // dd($now);
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
            break;

            case 'Último Trimestre':
                $now = \Carbon\Carbon::now()->month;
                $interval = 7;
                // dd($now);
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
            break;
            case 'Trimestre Actual':
                $now = \Carbon\Carbon::now()->month;
                $interval = 10;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
            break;
            // dd($date_from, $date_to);

            case 'Último Año':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $interval = 8;

                $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";

            break;

            case 'Año Actual':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $interval = 11;

                $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";

            break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $interval = 2;
            break;
        }

        $date_from_personalice = $request->date_from_personalice;
        $date_to_personalice = $request->date_to_personalice;

        $aux_date_from = \Carbon\Carbon::parse($date_from_personalice);
        $aux_date_to = \Carbon\Carbon::parse($date_to_personalice);


        if(isset(Session::get('_flash')['label_intervalo_navigation']))
        {
            if(Session::get('_flash')['label_intervalo_navigation'] != $request->label_intervalo && $request->label_intervalo != "Personalizado")
            {
                $flash['label_intervalo_navigation'] = $request->label_intervalo;
                Session::put('_flash',$flash);
            }
        }elseif($request->label_intervalo != "Personalizado"){
            $flash['label_intervalo_navigation'] = $request->label_intervalo;
            Session::put('_flash',$flash);
        }

        if(isset(Session::get('_flash')['label_intervalo_navigation']))
        {
            if(Session::get('_flash')['label_intervalo_navigation'] == "Ayer" || Session::get('_flash')['label_intervalo_navigation'] == "Hoy")
            {
                if($request->before_navigation == 1)
                {
                    $date_from = $aux_date_from->addDay()->toDateString();
                    $date_to = $aux_date_to->addDay()->toDateString();
                    $current_date = $date_from;
                }elseif($request->before_navigation == -1){
                    $date_from = $aux_date_from->subDay()->toDateString();
                    $date_to = $aux_date_to->subDay()->toDateString();
                    $current_date = $date_from;
                }else{
                    $flash['label_intervalo_navigation'] = 'otra cosa';
                    Session::put('_flash',$flash);
                }
            }
            if(Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior" || Session::get('_flash')['label_intervalo_navigation'] == "Semana Actual")
            {
                if($request->before_navigation == 1)
                {
                    $date_from = $aux_date_from->addWeek()->toDateString();
                    $date_to = $aux_date_to->addWeek()->toDateString();
                    $current_date = "Semana ".\Carbon\Carbon::parse($date_from)->weekOfYear;
                }elseif($request->before_navigation == -1){
                    $date_from = $aux_date_from->subWeek()->toDateString();
                    $date_to = $aux_date_to->subWeek()->toDateString();
                    $current_date = "Semana ".\Carbon\Carbon::parse($date_from)->weekOfYear;
                }else{
                    $flash['label_intervalo_navigation'] = 'otra cosa';
                    Session::put('_flash',$flash);
                }
            }
            if(Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior" || Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
            {
                // dd(Session::get('_flash')['label_intervalo_navigation']);
                if($request->before_navigation == 1)
                {
                    $date_from = $aux_date_from->addMonth()->toDateString();
                    $date_to = $aux_date_to->addMonth()->subDay(7)->endOfMonth()->toDateString();
                    $aux_month = \Carbon\Carbon::parse($date_from)->format('F');
                    if($aux_month == 'January')
                        $current_date = 'Enero';
                    if($aux_month == 'February')
                        $current_date = 'Febrero';
                    if($aux_month == 'March')
                        $current_date = 'Marzo';
                    if($aux_month == 'April')
                        $current_date = 'Abril';
                    if($aux_month == 'May')
                        $current_date = 'Mayo';
                    if($aux_month == 'June')
                        $current_date = 'Junio';
                    if($aux_month == 'July')
                        $current_date = 'Julio';
                    if($aux_month == 'August')
                        $current_date = 'Agosto';
                    if($aux_month == 'September')
                        $current_date = 'Septiembre';
                    if($aux_month == 'October')
                        $current_date = 'Octubre';
                    if($aux_month == 'November')
                        $current_date = 'Noviembre';
                    if($aux_month == 'December')
                        $current_date = 'Diciembre';

                }elseif($request->before_navigation == -1){
                    $date_from = $aux_date_from->subMonth()->toDateString();
                    $date_to = $aux_date_to->subMonth()->subDay(7)->endOfMonth()->toDateString();
                    $aux_month = \Carbon\Carbon::parse($date_from)->format('F');
                    if($aux_month == 'January')
                        $current_date = 'Enero';
                    if($aux_month == 'February')
                        $current_date = 'Febrero';
                    if($aux_month == 'March')
                        $current_date = 'Marzo';
                    if($aux_month == 'April')
                        $current_date = 'Abril';
                    if($aux_month == 'May')
                        $current_date = 'Mayo';
                    if($aux_month == 'June')
                        $current_date = 'Junio';
                    if($aux_month == 'July')
                        $current_date = 'Julio';
                    if($aux_month == 'August')
                        $current_date = 'Agosto';
                    if($aux_month == 'September')
                        $current_date = 'Septiembre';
                    if($aux_month == 'October')
                        $current_date = 'Octubre';
                    if($aux_month == 'November')
                        $current_date = 'Noviembre';
                    if($aux_month == 'December')
                        $current_date = 'Diciembre';
                }else{
                    $flash['label_intervalo_navigation'] = 'otra cosa';
                    Session::put('_flash',$flash);
                }
            }
            if(Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual" || Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre" || Session::get('_flash')['label_intervalo_navigation'] == "Último Trimestre")
            {
                if($request->before_navigation == 1)
                {
                    $date_from = $aux_date_from->addMonth(3)->toDateString();
                    $date_to = $aux_date_to->addMonth(3)->subDay()->endOfMonth()->toDateString();
                    // dd($date_to,$date_from);
                    $current_date = \Carbon\Carbon::parse($date_from)->quarter."T ".\Carbon\Carbon::parse($date_from)->year;
                }elseif($request->before_navigation == -1){
                    $date_from = $aux_date_from->subMonth(3)->toDateString();
                    $date_to = $aux_date_to->subMonth(3)->subDay()->endOfMonth()->toDateString();
                    $current_date = \Carbon\Carbon::parse($date_from)->quarter."T ".\Carbon\Carbon::parse($date_from)->year;
                }else{
                    $flash['label_intervalo_navigation'] = 'otra cosa';
                    Session::put('_flash',$flash);
                }
            }
            if(Session::get('_flash')['label_intervalo_navigation'] == "Año Actual" || Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
            {
                if($request->before_navigation == 1)
                {
                    $date_from = $aux_date_from->addYear()->toDateString();
                    $date_to = $aux_date_to->addYear()->toDateString();
                    $current_date = "Año ".\Carbon\Carbon::parse($date_from)->year;
                }elseif($request->before_navigation == -1){
                    $date_from = $aux_date_from->subYear()->toDateString();
                    $date_to = $aux_date_to->subYear()->subDay()->endOfMonth()->toDateString();
                    $current_date = "Año ".\Carbon\Carbon::parse($date_from)->year;
                }else{
                    $flash['label_intervalo_navigation'] = 'otra cosa';
                    Session::put('_flash',$flash);
                }
            }
        }

        //dd([$date_from, $date_to]);
        // dd('Intervalo Navegación',Session::get('_flash')['label_intervalo_navigation'],'date_from',$date_from, 'date_to',$date_to, 'interval',$interval,'before_navigation',$request->before_navigation);
        $sesion = $request->session()->all();
        $flash = $sesion['_flash'];
        $flash['intervalos'] = $interval;
        $flash['date_from_personalice'] = $date_from;
        $flash['date_to_personalice'] = $date_to;
        $flash['label_intervalo_navigation'] = Session::get('_flash')['label_intervalo_navigation'];
        $flash['current_date'] = $current_date;
        Session::put('_flash',$flash);

        // $url = Session::get('_previous')['url'];
        // dd($date_from, $date_to, Session::get('_flash')['date_from_personalice']);

        //$url = Session::flush();
        //var_dump($request->session()->all());
        //return $this->ResumenEnergiaPotencia($request->user_id);
        return \Redirect::back();
    }

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);

        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }

        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();
        // dd($domicilio);
        //dd($db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','2018-01-09')->groupBy('Hora')->get());
        // dd($db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=','2018-01-09')->where('date','<=','2018-01-15')->groupBy('date')->orderBy('date','ASC')->get());

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';

                if($tipo_count < 3)
                {
                    $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date',$date_from)->groupBy('Hora')->get();
                    foreach ($consumo_diario_energia as $consu) {
                        $eje[] = $consu->eje;
                        $consumo_activa[] = $consu->activa;

                        if($consu->activa >= $max_consumo_activa)
                            $max_consumo_activa = $consu->activa;
                        if($consu->inductiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->inductiva;
                        if($consu->capacitiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->capacitiva;

                            $consumo_inductiva[] = $consu->inductiva;
                            $consumo_capacitiva[] = $consu->capacitiva;
                    }
                    $balance = array();
                    if($tipo_count == 2)
                    {

                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date',$date_from)->groupBy('time')->get();
                        $t = 0;
                        foreach ($balance as $val)
                        {
                            // if($val->eje == $eje[$t])
                            // {
                                $balance2[$t]['eje'] = $val->eje;
                                $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                $balance2[$t]['balance_neto'] = $val->balance_neto;
                                // break;
                            // }else{
                            //     $balance2[$t]['consumo_energia'] = 0;
                            //     $balance2[$t]['generacion_energia'] = 0;
                            //     $balance2[$t]['balance_neto'] = 0;
                            // }
                                $t++;
                        }
                    }
                }else{
                    $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                    $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';

                if($tipo_count < 3)
                {
                    $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


                    foreach ($consumo_diario_energia as $consu) {
                        $eje[] = $consu->eje;
                        $consumo_activa[] = $consu->activa;

                        if($consu->activa >= $max_consumo_activa)
                            $max_consumo_activa = $consu->activa;
                        if($consu->inductiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->inductiva;
                        if($consu->capacitiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->capacitiva;

                        $consumo_inductiva[] = $consu->inductiva;
                        $consumo_capacitiva[] = $consu->capacitiva;
                    }

                    $balance = array();
                    if($tipo_count == 2)
                    {

                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                        $t = 0;
                        foreach ($balance as $val)
                        {
                            // if($val->eje == $eje[$t])
                            // {
                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                            $t++;
                        }
                    }
                    }else{
                        $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                        $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    }
                    break;

                    case '4':
                        $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                        {
                            $date_from = Session::get('_flash')['date_from_personalice'];
                            $date_to = Session::get('_flash')['date_to_personalice'];
                        }
                        $label_intervalo = 'Semana Anterior';

                        if($tipo_count < 3)
                        {
                            $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


                            foreach ($consumo_diario_energia as $consu) {
                                $eje[] = $consu->eje;
                                $consumo_activa[] = $consu->activa;

                                if($consu->activa >= $max_consumo_activa)
                                    $max_consumo_activa = $consu->activa;
                                if($consu->inductiva >= $max_consumo_activa)
                                    $max_consumo_activa = $consu->inductiva;
                                if($consu->capacitiva >= $max_consumo_activa)
                                    $max_consumo_activa = $consu->capacitiva;

                                    $consumo_inductiva[] = $consu->inductiva;
                                    $consumo_capacitiva[] = $consu->capacitiva;
                            }

                            $balance = array();
                    if($tipo_count == 2)
                    {
                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                        $t = 0;
                        foreach ($balance as $val)
                        {
                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                            $t++;
                        }
                    }
                }else{
                    $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                }
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                // dd(Session::get('_flash')['label_intervalo_navigation']);
                $label_intervalo = 'Mes Actual';

                if($tipo_count < 3)
                {
                    $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($consumo_diario_energia as $consu) {
                        $eje[] = $consu->eje;
                        $consumo_activa[] = $consu->activa;
                        if($consu->activa >= $max_consumo_activa)
                            $max_consumo_activa = $consu->activa;
                        if($consu->inductiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->inductiva;
                        if($consu->capacitiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->capacitiva;

                            $consumo_inductiva[] = $consu->inductiva;
                            $consumo_capacitiva[] = $consu->capacitiva;
                    }

                    $balance = array();
                    if($tipo_count == 2)
                    {

                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("DAY(date) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                        $t = 0;
                        foreach ($balance as $val)
                        {
                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                            $t++;
                        }
                    }
                }else{
                    $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("DAY(date) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("DAY(date) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }
            break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                if($tipo_count < 3)
                {
                    $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();


                    foreach ($consumo_diario_energia as $consu) {
                        $eje[] = $consu->eje;
                        $consumo_activa[] = $consu->activa;

                        if($consu->activa >= $max_consumo_activa)
                            $max_consumo_activa = $consu->activa;
                        if($consu->inductiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->inductiva;
                        if($consu->capacitiva >= $max_consumo_activa)
                            $max_consumo_activa = $consu->capacitiva;

                            $consumo_inductiva[] = $consu->inductiva;
                            $consumo_capacitiva[] = $consu->capacitiva;
                    }

                    $balance = array();
                    if($tipo_count == 2)
                    {
                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("DAY(date) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                        $t = 0;
                        foreach ($balance as $val)
                        {
                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                            $t++;
                        }
                    }
                }else{
                    $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("DAY(date) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("DAY(date) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }

                break;

                case '7':
                    $now = \Carbon\Carbon::now()->month;
                    $dont = 0;
                    if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                    {
                        $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
                        $date_from = Session::get('_flash')['date_from_personalice'];
                        $date_to = Session::get('_flash')['date_to_personalice'];
                        $dont = 1;
                    }
                    if($dont == 0)
                    {
                        if($now == 1 || $now == 2 || $now == 3)
                        {
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                                $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                            }
                            $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 4 || $now == 7 || $now == 10){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                            }
                            if($now == 4)
                            {
                                $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 7){
                                $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 10){
                                $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                            }
                        }elseif($now == 5 || $now == 8 || $now == 11){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                                $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                            }
                            if($now == 5)
                            {
                                $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 8){
                                $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 11){
                                $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                            }
                        }elseif($now == 6 || $now == 9 || $now == 12){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                                $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                            }
                            if($now == 6)
                            {
                                $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 9){
                                $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                            }elseif($now == 12){
                                $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                                $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                            }
                        }
                    }else{
                        // dd($now);
                        if($now == 1)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 4){
                            $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }
                    }
                    $label_intervalo = 'Ultimo Trimestre';
                    if($tipo_count < 3)
                    {
                        //$potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,Potencia_contratada p_optima"))->orderBy('eje')->get()->toArray();

                        $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                        // dd($consumo_diario_energia);
                        // foreach ($consumo_diario_energia as $consu) {
                        //     $eje[] = $consu->eje;
                        //     $consumo_activa[] = $consu->activa;
                        //     if($consu->activa >= $max_consumo_activa)
                        //         $max_consumo_activa = $consu->activa;
                        //     $consumo_inductiva[] = $consu->inductiva;
                        //     $consumo_capacitiva[] = $consu->capacitiva;
                        // }
                        $band = 0;
                        for ($t=0; $t < 3; $t++) {
                            foreach ($consumo_diario_energia as $val)
                            {
                                $band = 1;
                                if(!empty($val) || !is_null($val))
                                {
                                    if($val->eje == $eje[$t])
                                    {
                                        $consumo_activa[$t] = $val->activa;
                                        if($val->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $val->activa;
                                        if($val->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->inductiva;
                                        if($val->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->capacitiva;

                                        $consumo_inductiva[$t] = $val->inductiva;
                                        $consumo_capacitiva[$t] = $val->capacitiva;
                                        break;
                                    }else{
                                        $consumo_activa[$t] = 0;
                                        $consumo_inductiva[$t] = 0;
                                        $consumo_capacitiva[$t] = 0;
                                    }
                                }else{
                                    $consumo_activa[$t] = 0;
                                    $consumo_inductiva[$t] = 0;
                                    $consumo_capacitiva[$t] = 0;
                                }
                            }
                            if($band == 0)
                            {
                                $consumo_activa[$t] = 0;
                                $consumo_inductiva[$t] = 0;
                                $consumo_capacitiva[$t] = 0;
                            }
                        }
                        // dd($consumo_diario_energia,$consumo_activa,$consumo_capacitiva, $consumo_inductiva);
                        $balance = array();
                        if($tipo_count == 2)
                        {
                            $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                            for ($t=0; $t < 3; $t++)
                            {
                                foreach ($balance as $val)
                                {
                                    if($val->eje == $eje[$t])
                                    {
                                        $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                        $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                        $balance2[$t]['balance_neto'] = $val->balance_neto;
                                        break;
                                    }else{
                                        $balance2[$t]['consumo_energia'] = 0;
                                        $balance2[$t]['generacion_energia'] = 0;
                                        $balance2[$t]['balance_neto'] = 0;
                                    }
                                }
                            }
                        }
                    }else{
                        $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                        $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    }

                    break;

                    case '10':
                        $now = \Carbon\Carbon::now()->month;
                        $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                        {
                            $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                            $date_from = Session::get('_flash')['date_from_personalice'];
                            $date_to = Session::get('_flash')['date_to_personalice'];
                            $dont = 1;
                        }
                        if($now == 1 || $now == 2 || $now == 3)
                        {
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                                $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                            }
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 4 || $now == 5 || $now == 6){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                                $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                            }
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7 || $now == 8 || $now == 9){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                                $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                            }
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10 || $now == 11 || $now == 12){
                            if($dont == 0)
                            {
                                $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                                $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                            }
                            $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                        }
                        $label_intervalo = 'Trimestre Actual';

                        if($tipo_count < 3)
                        {
                            //$potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,Potencia_contratada p_optima"))->orderBy('eje')->get()->toArray();

                            $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                            // dd($consumo_diario_energia);

                            // $t = 0;
                            for ($t=0; $t < 3; $t++)
                            {
                                foreach ($consumo_diario_energia as $val)
                                {
                                    if($val->eje == $eje[$t])
                                    {
                                        $consumo_activa[$t] = $val->activa;

                                        if($val->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $val->activa;
                                        if($val->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->inductiva;
                                        if($val->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->capacitiva;

                                        $consumo_inductiva[$t] = $val->inductiva;
                                        $consumo_capacitiva[$t] = $val->capacitiva;
                                        break;
                                    }else{
                                        $consumo_activa[$t] = 0;
                                        $consumo_inductiva[$t] = 0;
                                        $consumo_capacitiva[$t] = 0;
                                    }
                                }
                            }
                            // dd($eje);
                            // for ($t=0; $t < 3; $t++) {
                            //     if(isset($consumo_diario_energia[$t]->activa))
                            //     {
                            //         dd($eje[$t],$consumo_diario_energia[$t]->eje);
                            //         if($eje[$t] == $consumo_diario_energia[$t]->eje)
                            //         {
                            //             $consumo_activa[$t] = $consumo_diario_energia[$t]->activa;

                            //             if($consumo_diario_energia[$t]->activa >= $max_consumo_activa)
                            //                 $max_consumo_activa = $consumo_diario_energia[$t]->activa;
                            //             if($consumo_diario_energia[$t]->inductiva >= $max_consumo_activa)
                            //                 $max_consumo_activa = $consumo_diario_energia[$t]->inductiva;
                            //             if($consumo_diario_energia[$t]->capacitiva >= $max_consumo_activa)
                            //                 $max_consumo_activa = $consumo_diario_energia[$t]->capacitiva;

                            //                 $consumo_inductiva[$t] = $consumo_diario_energia[$t]->inductiva;
                            //                 $consumo_capacitiva[$t] = $consumo_diario_energia[$t]->capacitiva;
                            //         }

                            //     }else{
                            //         $consumo_activa[$t] = 0;
                            //         $consumo_inductiva[$t] = 0;
                            //         $consumo_capacitiva[$t] = 0;
                            //     }
                            // }
                            // dd($consumo_activa, $consumo_capacitiva, $consumo_inductiva);
                            // foreach ($consumo_diario_energia as $consu) {
                            //     // $eje[] = $consu->eje;
                            //     // if($t == 0)
                            //     // {
                            //         $consumo_activa[$t] = $consu->activa;
                            //         if($consu->activa >= $max_consumo_activa)
                            //             $max_consumo_activa = $consu->activa;
                            //         $consumo_inductiva[$t] = $consu->inductiva;
                            //         $consumo_capacitiva[$t] = $consu->capacitiva;
                            //         $t++;
                            //     // }
                            // }
                            $balance = array();
                            if($tipo_count == 2)
                            {
                                $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                                for ($t=0; $t < 3; $t++)
                                {
                                    foreach ($balance as $val)
                                    {
                                        if($val->eje == $eje[$t])
                                        {
                                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                                            break;
                                        }else{
                                            $balance2[$t]['consumo_energia'] = 0;
                                            $balance2[$t]['generacion_energia'] = 0;
                                            $balance2[$t]['balance_neto'] = 0;
                                        }
                                    }
                                }
                            }

                        }else{
                            $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                            $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                        }

                        break;

                    case '11':
                        $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                        $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                        $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                        {
                            $date_from = Session::get('_flash')['date_from_personalice'];
                            $date_to = Session::get('_flash')['date_to_personalice'];
                            $dont = 1;
                        }
                        $label_intervalo = 'Año Actual';
                        if($dont == 0)
                        {
                            $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                            $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                        }else{
                            $a_o = \Carbon\Carbon::parse($date_from)->year;
                            $eje[0] = "Enero(".$a_o.")";
                            $eje[1] = "Febrero(".$a_o.")";
                            $eje[2] = "Marzo(".$a_o.")";
                            $eje[3] = "Abril(".$a_o.")";
                            $eje[4] = "Mayo(".$a_o.")";
                            $eje[5] = "Junio(".$a_o.")";
                            $eje[6] = "Julio(".$a_o.")";
                            $eje[7] = "Agosto(".$a_o.")";
                            $eje[8] = "Septiembre(".$a_o.")";
                            $eje[9] = "Octubre(".$a_o.")";
                            $eje[10] = "Noviembre(".$a_o.")";
                            $eje[11] = "Diciembre(".$a_o.")";
                        }

                        if($tipo_count < 3)
                        {
                            $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                            for ($t=0; $t < 12; $t++)
                            {
                                foreach ($consumo_diario_energia as $val)
                                {
                                    if($val->eje == $eje[$t])
                                    {
                                        $consumo_activa[$t] = $val->activa;

                                        if($val->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $val->activa;
                                        if($val->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->inductiva;
                                        if($val->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->capacitiva;

                                            $consumo_inductiva[$t] = $val->inductiva;
                                            $consumo_capacitiva[$t] = $val->capacitiva;
                                            break;
                                    }else{
                                        $consumo_activa[$t] = 0;
                                        $consumo_inductiva[$t] = 0;
                                        $consumo_capacitiva[$t] = 0;
                                    }
                                }
                            }

                            // foreach ($consumo_diario_energia as $consu) {
                            //     $eje[] = $consu->eje;
                            //     $consumo_activa[] = $consu->activa;
                            //     if($consu->activa >= $max_consumo_activa)
                            //         $max_consumo_activa = $consu->activa;
                            //     $consumo_inductiva[] = $consu->inductiva;
                            //     $consumo_capacitiva[] = $consu->capacitiva;
                            // }

                            $balance = array();
                            if($tipo_count == 2)
                            {
                                $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                                for ($t=0; $t < 12; $t++)
                                {
                                    foreach ($balance as $val)
                                    {
                                        if($val->eje == $eje[$t])
                                        {
                                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                                            break;
                                        }else{
                                            $balance2[$t]['consumo_energia'] = 0;
                                            $balance2[$t]['generacion_energia'] = 0;
                                            $balance2[$t]['balance_neto'] = 0;
                                        }
                                    }
                                }
                            }
                        }else{
                            $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                            $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                        }

                        break;

                    case '8':
                        $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                        $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                        {
                            $date_from = Session::get('_flash')['date_from_personalice'];
                            $date_to = Session::get('_flash')['date_to_personalice'];
                            $dont = 1;
                        }
                        $label_intervalo = 'Último Año';
                        if($dont == 0)
                        {
                            $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                            $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                        }else{
                            $a_o = \Carbon\Carbon::parse($date_from)->year;
                            $eje[0] = "Enero(".$a_o.")";
                            $eje[1] = "Febrero(".$a_o.")";
                            $eje[2] = "Marzo(".$a_o.")";
                            $eje[3] = "Abril(".$a_o.")";
                            $eje[4] = "Mayo(".$a_o.")";
                            $eje[5] = "Junio(".$a_o.")";
                            $eje[6] = "Julio(".$a_o.")";
                            $eje[7] = "Agosto(".$a_o.")";
                            $eje[8] = "Septiembre(".$a_o.")";
                            $eje[9] = "Octubre(".$a_o.")";
                            $eje[10] = "Noviembre(".$a_o.")";
                            $eje[11] = "Diciembre(".$a_o.")";
                        }
                        if($tipo_count < 3)
                        {
                            $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                            for ($t=0; $t < 12; $t++)
                            {
                                foreach ($consumo_diario_energia as $val)
                                {
                                    if($val->eje == $eje[$t])
                                    {
                                        $consumo_activa[$t] = $val->activa;

                                        if($val->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $val->activa;
                                        if($val->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->inductiva;
                                        if($val->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $val->capacitiva;

                                            $consumo_inductiva[$t] = $val->inductiva;
                                            $consumo_capacitiva[$t] = $val->capacitiva;
                                            break;
                                    }else{
                                        $consumo_activa[$t] = 0;
                                        $consumo_inductiva[$t] = 0;
                                        $consumo_capacitiva[$t] = 0;
                                    }
                                }
                            }

                            // foreach ($consumo_diario_energia as $consu) {
                            //     $eje[] = $consu->eje;
                            //     $consumo_activa[] = $consu->activa;
                            //     if($consu->activa >= $max_consumo_activa)
                            //         $max_consumo_activa = $consu->activa;
                            //     $consumo_inductiva[] = $consu->inductiva;
                            //     $consumo_capacitiva[] = $consu->capacitiva;
                            // }
                            $balance = array();
                            if($tipo_count == 2)
                            {
                                $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                                for ($t=0; $t < 12; $t++)
                                {
                                    foreach ($balance as $val)
                                    {
                                        if($val->eje == $eje[$t])
                                        {
                                            $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                            $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                            $balance2[$t]['balance_neto'] = $val->balance_neto;
                                            break;
                                        }else{
                                            $balance2[$t]['consumo_energia'] = 0;
                                            $balance2[$t]['generacion_energia'] = 0;
                                            $balance2[$t]['balance_neto'] = 0;
                                        }
                                    }
                                }
                            }
                        }else{
                            $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                            $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                        }

                        break;

                    case '9':
                        $date_from = Session::get('_flash')['date_from_personalice'];
                        $date_to = Session::get('_flash')['date_to_personalice'];
                        $label_intervalo = 'Personalizado';
                        $dates = [];
                        $date_from_Car = \Carbon\Carbon::parse($date_from);
                        $date_to_Car = \Carbon\Carbon::parse($date_to);
                        $totalActiva = 0;

                        if($tipo_count < 3)
                        {
                            if($date_to != $date_from)
                            {
                                for($date = $date_from_Car; $date->lte($date_to_Car); $date->addDay()) {
                                    $dates[] = $date->format('Y-m-d');
                                }
                                $eje = $dates;
                                $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("date eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                                // dd($consumo_diario_energia);

                                for($t = 0; $t < count($dates); $t++)
                                {
                                    if(isset($consumo_diario_energia[$t]->activa))
                                    {
                                        $consumo_activa[$t] = $consumo_diario_energia[$t]->activa;
                                        $totalActiva += $consumo_diario_energia[$t]->activa;

                                        if($consumo_diario_energia[$t]->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $consumo_diario_energia[$t]->activa;
                                        if($consumo_diario_energia[$t]->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $consumo_diario_energia[$t]->inductiva;
                                        if($consumo_diario_energia[$t]->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $consumo_diario_energia[$t]->capacitiva;

                                            $consumo_inductiva[$t] = $consumo_diario_energia[$t]->inductiva;
                                            $consumo_capacitiva[$t] = $consumo_diario_energia[$t]->capacitiva;
                                    }else{
                                        $consumo_activa[$t] = 0;
                                        $consumo_inductiva[$t] = 0;
                                        $consumo_capacitiva[$t] = 0;
                                        $totalActiva += 0;
                                    }
                                }

                                // foreach ($consumo_diario_energia as $consu) {
                                //     $eje[] = $consu->eje;
                                //     $consumo_activa[] = $consu->activa;
                                //     if($consu->activa >= $max_consumo_activa)
                                //         $max_consumo_activa = $consu->activa;
                                //     $consumo_inductiva[] = $consu->inductiva;
                                //     $consumo_capacitiva[] = $consu->capacitiva;
                                // }

                                $balance = array();
                                if($tipo_count == 2)
                                {
                                    $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("date eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                                    for ($t=0; $t < count($dates); $t++)
                                    {
                                        if(isset($balance[$t]->generacion_energia))
                                        {
                                            $balance2[$t]['consumo_energia'] = $balance[$t]->consumo_energia;
                                            $balance2[$t]['generacion_energia'] = $balance[$t]->generacion_energia;
                                            $balance2[$t]['balance_neto'] = $balance[$t]->balance_neto;
                                        }else{
                                            $balance2[$t]['consumo_energia'] = 0;
                                            $balance2[$t]['generacion_energia'] = 0;
                                            $balance2[$t]['balance_neto'] = 0;
                                        }
                                    }
                                }
                            }else{
                                $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Hora')->get();

                                foreach ($consumo_diario_energia as $consu) {
                                    $eje[] = $consu->eje;
                                    $consumo_activa[] = $consu->activa;

                                    if($consu->activa >= $max_consumo_activa)
                                        $max_consumo_activa = $consu->activa;
                                    if($consu->inductiva >= $max_consumo_activa)
                                        $max_consumo_activa = $consu->inductiva;
                                    if($consu->capacitiva >= $max_consumo_activa)
                                        $max_consumo_activa = $consu->capacitiva;

                                    $consumo_inductiva[] = $consu->inductiva;
                                    $consumo_capacitiva[] = $consu->capacitiva;
                                }
                                $balance = array();
                                if($tipo_count == 2)
                                {

                                    $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date',$date_from)->groupBy('time')->get();
                                    $t = 0;
                                    foreach ($balance as $val)
                                    {
                                        // if($val->eje == $eje[$t])
                                        // {
                                        $balance2[$t]['eje'] = $val->eje;
                                        $balance2[$t]['consumo_energia'] = $val->consumo_energia;
                                        $balance2[$t]['generacion_energia'] = $val->generacion_energia;
                                        $balance2[$t]['balance_neto'] = $val->balance_neto;
                                        // break;
                                        // }else{
                                        //     $balance2[$t]['consumo_energia'] = 0;
                                            //     $balance2[$t]['generacion_energia'] = 0;
                                            //     $balance2[$t]['balance_neto'] = 0;
                                            // }
                                        $t++;
                                    }
                                }
                                // $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->get();
                            }
                        }else{
                            if($date_to != $date_from)
                            {
                                $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("date eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                                $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("date eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                            }else{
                                $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                                $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                            }
                        }
                        break;

                            default:
                                $date_from = \Carbon\Carbon::now()->toDateString();
                                $date_to = $date_from;
                                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                                {
                                    $date_from = Session::get('_flash')['date_from_personalice'];
                                    $date_to = Session::get('_flash')['date_to_personalice'];
                                }
                                $label_intervalo = 'Hoy';

                                if($tipo_count < 3)
                                {
                                    $consumo_diario_energia = $db->table('Consumo_Diario_Energia')->select(\DB::raw("Hora eje,SUM(`Energia Activa (kWh)`) activa, SUM(`Energia Reactiva Inductiva (kVArh)`) inductiva, SUM(`Energia Reactiva Capacitiva (kVArh)`) capacitiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Hora')->get();
                                    foreach ($consumo_diario_energia as $consu) {
                                        $eje[] = $consu->eje;
                                        $consumo_activa[] = $consu->activa;

                                        if($consu->activa >= $max_consumo_activa)
                                            $max_consumo_activa = $consu->activa;
                                        if($consu->inductiva >= $max_consumo_activa)
                                            $max_consumo_activa = $consu->inductiva;
                                        if($consu->capacitiva >= $max_consumo_activa)
                                            $max_consumo_activa = $consu->capacitiva;

                                            $consumo_inductiva[] = $consu->inductiva;
                                            $consumo_capacitiva[] = $consu->capacitiva;
                                    }

                                    $balance = array();
                                    if($tipo_count == 2)
                                        $balance = $db->table('Balance_Neto_Diario')->select(\DB::raw("time eje,SUM(`Consumo Energia (kWh)`) consumo_energia, SUM(`Generacion Energia (kWh)`) generacion_energia, SUM(`Balance Neto (kWh)`) balance_neto"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->get();
                                }else{
                                    $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (kWh)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                                    $consumo_GN_Nm3 = $db->table('Consumo_GN_Nm3')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Consumo GN (Nm3)`) consumo"))->where('date',$date_from)->groupBy('time')->get();
                                }

                                break;
                        }

                        $user = Auth::user();//usuario logeado
                        $titulo = 'Energía y Potencia';//Título del content

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
                            $index = 0;
                            if($contador2->database == 'Prueba_Contador_6.0_V3' && ($contador2->tarifa != 2 && $contador2->tarifa != 3))
                            {
                                $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

                                $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
                                $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
                                $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
                                $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
                                $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
                                $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
                                $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();

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
                                $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
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

                                // $db_excesos2 = $db->table('`ZPI_Dias_Excesos_y_Precio_Contratada`')->select(\DB::raw("((1 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `P1`,((0.5 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `P2`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `P3`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `P4`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `P5`,((0.17 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `P6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

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

                            $db_Venta_Energia = array();
                            $total_ventas = 0;
                            if($contador2->tipo == 2)
                            {
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

                                $generacion = $db->table('Generacion_Energia_Activa_y_Reactiva')->select(\DB::raw("Periodo,SUM(`Generación Energia Activa (kWh)`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                                $h = 0;
                                if(!empty($db_Venta_Energia))
                                {
                                    foreach ($aux_Venta_Energia[0] as $ventas) {
                                        $total_ventas = $total_ventas + $ventas;
                                        $h++;
                                    }
                                }
                            }

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

            $data_labels = ResumenEnergiaController::getLabelsPlot($data_calculos);
            $data_calculos["data_labels"] = $data_labels;

            $dataPlotting = ResumenEnergiaController::createConsumptionPlots($data_calculos);
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
                        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id =". $id);

                        if(is_null($aux_current_count) || empty($aux_current_count)){
                            \DB::insert("INSERT INTO current_count (label_current_count, user_id) VALUES ('".$current_count."',".$id.")");
                        }
                        else{
                            // dd("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
                            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
                        }
                        // dd(Session::get('_flash')['current_count']);
                        // dd($current_count,Session::get('_flash')['current_count'],$contador);
                        // $flash['current_count'] = $contador2->count_label;
                        // Session::put('_flash',$flash);

                        // dd("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
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
                                return view('resumen_energia_potencia.resumen_energia_potencia',compact('user','titulo','cliente','id','ctrl','periodos2','EAct','p_contratada','periodos_coste','coste_potencia','array_total','Energia_Act','Energia_Reac_Cap','Energia_Reac_Induc', 'coste_termino_energia', 'consumo_diario_energia','eje','consumo_activa','consumo_capacitiva','consumo_inductiva','label_intervalo','date_from','date_to','tipo_count','db_Venta_Energia','total_ventas','balance','generacion','interval','contador_label','domicilio','potencia_optima','dir_image_count','energia_activa_max','max_consumo_activa','balance2','tipo_tarifa','peri', 'dataSubperiodo', 'dataPlotting'));
                            }else{
                                $total1 = 0;
                                $total2 = 0;
                                foreach ($consumo_GN_kWh as $value) {
                                    $total1 += $value->consumo;
                                }
                                foreach ($consumo_GN_Nm3 as $value) {
                                    $total2 += $value->consumo;
                                }
                                return view('resumen_gn.resumen_gn',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from','date_to','tipo_count','interval','contador_label','consumo_GN_kWh','consumo_GN_Nm3','direccion','coste_termino_fijo','coste_termino_variable','QD_contratado','PCS','total1','total2','tarifa','domicilio','dir_image_count','energia_activa_max','balance2','tipo_tarifa','peri'));
                            }
                        }
                        return \Redirect::to('https://submeter.es/');
                        // if($tipo_count < 3)
                        // {
                        //     return view('resumen_energia_potencia.resumen_energia_potencia',compact('user','titulo','cliente','id','ctrl','periodos2','EAct','p_contratada','periodos_coste','coste_potencia','array_total','Energia_Act','Energia_Reac_Cap','Energia_Reac_Induc', 'coste_termino_energia', 'consumo_diario_energia','eje','consumo_activa','consumo_capacitiva','consumo_inductiva','label_intervalo','date_from','date_to','tipo_count','db_Venta_Energia','total_ventas','balance','generacion','interval','contador_label','domicilio','potencia_optima','dir_image_count','energia_activa_max','max_consumo_activa','balance2','tipo_tarifa','peri'));
                        // }else{
                        //     return view('resumen_gn.resumen_gn',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from','date_to','tipo_count','interval','contador_label','consumo_GN_kWh','consumo_GN_Nm3','domicilio','dir_image_count','energia_activa_max','balance2','tipo_tarifa','peri'));
                        // }
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

    function CargarSubPeriodo(Request $request)
    {
        $session = $request->session()->all();
        $flash = $session['_flash'];
        $flash['label_intervalo_navigation'] = $request->get("label_intervalo");
        Session::put('_flash', []);
        return $this->ConfigurarIntervaloNavegacion($request);
    }

    function ConsumoEnergia($id,Request $request)
    {
        $contador = strtolower(request()->input('contador'));
        $tipo_count = strtolower(request()->input('tipo'));
        if(empty($tipo_count))
        {
            $tipo_count = Count::where('user_id',$id)->first()->tipo;
            $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

        }
        $dates =array();
        $interval = Session::get('_flash')['intervalos'];
        $eje = array();
        $eje2 = array();
        $consumo_activa = array();
        $consumo_induc = array();
        $consumo_cap = array();
        $totalInduc = 0;
        $totalCapa = 0;
        $totalActiva = 0;
        $generacion2 = array();
        $db_Generacion = array();
        // dd(Session::get('_flash')['current_count']);

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        $aux_current_count = $aux_current_count[0]->label_current_count;
        if(!is_null($aux_current_count) || !empty($aux_current_count))
        {
            if(isset(Session::get('_flash')['current_count']))
            {
                if(Session::get('_flash')['current_count'] != $aux_current_count)
                {
                    $flash['current_count'] = $aux_current_count;
                    $flash['intervalos'] = $interval;
                    Session::put('_flash',$flash);
                }
            }
        }

        if(!isset(Session::get('_flash')['current_count']))
        {
            if(empty($contador))
            {
                $contador2 = Count::where('user_id',$id)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }else{
            $current_count = Session::get('_flash')['current_count'];
            if(empty($contador))
            {
                $contador2 = Count::where('count_label',$current_count)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }
        // dd($id,$tipo_count,$contador2);
        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');
        $aux_max_consumo = 0;
        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                $number_days = 1;

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa"))->where('date',$date_from)->groupBy('time')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Generacion Energia`) generacion_energia"))->where('date',$date_from)->groupBy('time')->get();
                }

                //$consumo_energia_activa = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date = '".$date_from."' GROUP BY time");

                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    if($aux_max_consumo <= $consu->activa)
                        $aux_max_consumo = $consu->activa;
                    $totalActiva += $consu->activa;
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date',$date_from)->groupBy('time')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalInduc += $consu->Induc;
                    $totalCapa += $consu->Capa;
                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                $number_days = 1;

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }

                // $consumo_energia_activa = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    $totalActiva += $consu->activa;
                    if($aux_max_consumo <= $consu->activa)
                        $aux_max_consumo = $consu->activa;
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalInduc += $consu->Induc;
                    $totalCapa += $consu->Capa;
                }
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                $number_days = 1;

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }

                // $consumo_energia_activa = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    $totalActiva += $consu->activa;
                    if($aux_max_consumo <= $consu->activa)
                        $aux_max_consumo = $consu->activa;
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalInduc += $consu->Induc;
                    $totalCapa += $consu->Capa;
                }
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                $number_days = 11;
                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }

                // $consumo_energia_activa = \DB::select("SELECT DAY(date) eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    $totalActiva += $consu->activa;
                    if($aux_max_consumo <= $consu->activa)
                        $aux_max_consumo = $consu->activa;
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("DAY(date) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalInduc += $consu->Induc;
                    $totalCapa += $consu->Capa;
                }
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                $number_days = 11;
                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("DAY(date) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("DAY(date) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                }

                // $consumo_energia_activa = \DB::select("SELECT DAY(date) eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    $totalActiva += $consu->activa;
                    if($aux_max_consumo <= $consu->activa)
                        $aux_max_consumo = $consu->activa;
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("DAY(date) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT DAY(date) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalInduc += $consu->Induc;
                    $totalCapa += $consu->Capa;
                }
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $number_days = 1;
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 3; $t++)
                    {
                        foreach ($db_Generacion as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                                break;
                            }else{
                                $generacion2[$t]['generacion_energia'] = 0;
                            }
                        }
                    }
                    $db_Generacion = $generacion2;
                }

                // $consumo_energia_activa = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($consumo_energia_activa as $consu) {
                //     $eje[] = $consu->eje;
                //     $consumo_activa[] = $consu->activa;
                //     $totalActiva += $consu->activa;
                // }
                for ($t=0; $t < 3; $t++) {

                    $consumo_activa[$t] = 0;
                    $totalActiva += 0;

                    foreach($consumo_energia_activa as $value)
                    {
                        if($value->eje == $eje[$t])
                        {
                            $consumo_activa[$t] = $value->activa;
                            if($aux_max_consumo <= $value->activa)
                                $aux_max_consumo = $value->activa;
                            $totalActiva += $value->activa;
                            break;
                        }
                    }
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();


                // foreach ($consumo_energia_reactiva as $consu) {
                //     $eje2[] = $consu->eje;
                //     $consumo_induc[] = $consu->Induc;
                //     $consumo_cap[] = $consu->Capa;
                //     $totalInduc += $consu->Induc;
                //     $totalCapa += $consu->Capa;
                // }
                // dd($consumo_energia_reactiva,$eje);
                for ($t=0; $t < 3; $t++)
                {
                    $consumo_induc[$t] = 0;
                    $consumo_cap[$t] = 0;
                    $totalInduc += 0;
                    $totalCapa += 0;
                    foreach ($consumo_energia_reactiva as $value)
                    {
                        if($value->eje == $eje[$t])
                        {
                            $consumo_induc[$t] = $value->Induc;
                            $consumo_cap[$t] = $value->Capa;
                            $totalInduc += $value->Induc;
                            $totalCapa += $value->Capa;
                            break;
                        }
                    }
                }

                break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $number_days = 1;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    // dd($db_Generacion);
                    for ($t=0; $t < 3; $t++)
                    {
                        foreach ($db_Generacion as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                                break;
                            }else{
                                $generacion2[$t]['generacion_energia'] = 0;
                            }
                        }
                    }
                    $db_Generacion = $generacion2;
                }


                // foreach ($consumo_energia_activa as $consu) {
                //     $eje[] = $consu->eje;
                //     $consumo_activa[] = $consu->activa;
                //     $totalActiva += $consu->activa;
                // }

                for ($t=0; $t < 3; $t++) {
                    foreach ($consumo_energia_activa as $value) {
                        if($eje[$t] == $value->eje)
                        {
                            $consumo_activa[$t] = $value->activa;
                            if($aux_max_consumo <= $value->activa)
                                $aux_max_consumo = $value->activa;
                            $totalActiva += $value->activa;
                            break;
                        }else{
                            $consumo_activa[$t] = 0;
                            $totalActiva += 0;
                        }
                    }
                    if(!isset($consumo_energia_activa[0]->activa))
                    {
                        $consumo_activa[$t] = 0;
                        $totalActiva += 0;
                    }
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // foreach ($consumo_energia_reactiva as $consu) {
                //     $eje2[] = $consu->eje;
                //     $consumo_induc[] = $consu->Induc;
                //     $consumo_cap[] = $consu->Capa;
                //     $totalInduc += $consu->Induc;
                //     $totalCapa += $consu->Capa;
                // }
                for ($t=0; $t < 3; $t++) {
                    foreach ($consumo_energia_reactiva as $value) {
                        if($eje[$t] == $value->eje)
                        {
                            $consumo_induc[$t] = $value->Induc;
                            $consumo_cap[$t] = $value->Capa;
                            $totalInduc += $value->Induc;
                            $totalCapa += $value->Capa;
                            break;
                        }else{
                            $consumo_induc[$t] = 0;
                            $consumo_cap[$t] = 0;
                            $totalInduc += 0;
                            $totalCapa += 0;
                        }
                    }
                    if(!isset($consumo_energia_reactiva[0]->Capa))
                    {
                        $consumo_induc[$t] = 0;
                        $consumo_cap[$t] = 0;
                        $totalInduc += 0;
                        $totalCapa += 0;
                    }
                }

                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                $number_days = 11;
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 12; $t++)
                    {
                        foreach ($db_Generacion as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                                break;
                            }else{
                                $generacion2[$t]['generacion_energia'] = 0;
                            }
                        }
                    }
                    $db_Generacion = $generacion2;
                }

                // $consumo_energia_activa = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($consumo_energia_activa as $consu) {
                //     $eje[] = $consu->eje;
                //     $consumo_activa[] = $consu->activa;
                //     $totalActiva += $consu->activa;
                // }

                $band = 0;

                for ($t=0; $t < 12; $t++)
                {
                    foreach ($consumo_energia_activa as $val)
                    {
                        $band = 1;
                        if($val->eje == $eje[$t])
                        {
                            $consumo_activa[$t] = $val->activa;
                            $totalActiva += $val->activa;
                            if($aux_max_consumo <= $val->activa)
                                $aux_max_consumo = $val->activa;
                            break;
                        }else{
                            $consumo_activa[$t] = 0;
                            $totalActiva += 0;
                        }
                    }
                    if($band == 0)
                    {
                        $consumo_activa[$t] = 0;
                        $totalActiva += 0;
                    }
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($consumo_energia_reactiva as $consu) {
                //     $eje2[] = $consu->eje;
                //     $consumo_induc[] = $consu->Induc;
                //     $consumo_cap[] = $consu->Capa;
                //     $totalInduc += $consu->Induc;
                //     $totalCapa += $consu->Capa;
                // }
                $band = 0;
                for ($t=0; $t < 12; $t++)
                {
                    foreach ($consumo_energia_reactiva as $val)
                    {
                        $band = 1;
                        if($val->eje == $eje[$t])
                        {
                            $consumo_induc[$t] = $val->Induc;
                            $consumo_cap[$t] = $val->Capa;
                            $totalInduc += $val->Induc;
                            $totalCapa += $val->Capa;
                            break;
                        }else{
                            $consumo_induc[$t] = 0;
                            $consumo_cap[$t] = 0;
                            $totalInduc += 0;
                            $totalCapa += 0;
                        }
                    }
                    if($band == 0)
                    {
                        $consumo_induc[$t] = 0;
                        $consumo_cap[$t] = 0;
                        $totalInduc += 0;
                        $totalCapa += 0;
                    }
                }
                break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $number_days = 19;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }
                // dd($date_from, $date_to);
                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    for ($t=0; $t < 12; $t++)
                    {
                        $generacion2[$t]['generacion_energia'] = 0;
                        foreach ($db_Generacion as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                                break;
                            }
                        }
                    }
                    $db_Generacion = $generacion2;
                }

                // $consumo_energia_activa = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($consumo_energia_activa as $consu) {
                //     $eje[] = $consu->eje;
                //     $consumo_activa[] = $consu->activa;
                //     $totalActiva += $consu->activa;
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $consumo_activa[$t] = 0;
                    $totalActiva += 0;
                    foreach ($consumo_energia_activa as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $consumo_activa[$t] = $val->activa;
                            $totalActiva += $val->activa;
                            if($aux_max_consumo <= $val->activa)
                                $aux_max_consumo = $val->activa;
                            break;
                        }
                    }
                }

                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($consumo_energia_reactiva as $consu) {
                //     $eje2[] = $consu->eje;
                //     $consumo_induc[] = $consu->Induc;
                //     $consumo_cap[] = $consu->Capa;
                //     $totalInduc += $consu->Induc;
                //     $totalCapa += $consu->Capa;
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $consumo_induc[$t] = 0;
                    $consumo_cap[$t] = 0;
                    $totalInduc += 0;
                    $totalCapa += 0;
                    foreach ($consumo_energia_reactiva as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $consumo_induc[$t] = $val->Induc;
                            $consumo_cap[$t] = $val->Capa;
                            $totalInduc += $val->Induc;
                            $totalCapa += $val->Capa;
                            break;
                        }
                    }
                }

                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                $dates = [];
                $date_from_Car = \Carbon\Carbon::parse($date_from);
                $date_to_Car = \Carbon\Carbon::parse($date_to);
                $number_days = $date_to_Car->diffInDays($date_from_Car);
                // dd($date_to_Car->diffInDays($date_from_Car));

                if($date_from != $date_to)
                {
                    for($date = $date_from_Car; $date->lte($date_to_Car); $date->addDay()) {
                        $dates[] = $date->format('Y-m-d');
                    }
                    // $eje = $dates;
                    $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("date eje,SUM(`Energia Activa (kWh)`) activa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                    if($contador2->tipo == 2)
                    {
                        $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("date eje,SUM(`Generacion Energia`) generacion_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                        // dd(count($dates),$db_Generacion,count($db_Generacion));

                        for ($t=0; $t < count($dates); $t++)
                        {
                            if(isset($db_Generacion[$t]->generacion_energia))
                            {
                                $generacion2[$t]['generacion_energia'] = $db_Generacion[$t]->generacion_energia;
                            }else{
                                $generacion2[$t]['generacion_energia'] = 0;
                            }
                        }
                        $db_Generacion = $generacion2;
                    }
                    // dd($db_Generacion);
                    // dd($db_Generacion, $consumo_energia_activa);

                    // $consumo_energia_activa = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date = '".$date_from."' GROUP BY time");
                    // foreach ($consumo_energia_activa as $consu) {
                    //     $eje[] = $consu->eje;
                    //     $consumo_activa[] = $consu->activa;
                    //     $totalActiva += $consu->activa;
                    // }
                    for ($t=0; $t < count($dates); $t++)
                    {
                        if(isset($consumo_energia_activa[$t]->activa))
                        {
                            $consumo_activa[$t] = $consumo_energia_activa[$t]->activa;
                            $totalActiva += $consumo_energia_activa[$t]->activa;
                            if($aux_max_consumo <= $consumo_energia_activa[$t]->activa)
                                $aux_max_consumo = $consumo_energia_activa[$t]->activa;
                        }else{
                            $consumo_activa[$t] = 0;
                            $totalActiva += 0;
                        }
                    }
                    $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("date eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                    // $consumo_energia_reactiva = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date = '".$date_from."' GROUP BY time");
                    for ($t=0; $t < count($dates); $t++)
                    {
                        if(isset($consumo_energia_reactiva[$t]->Induc))
                        {
                            $consumo_induc[$t] = $consumo_energia_reactiva[$t]->Induc;
                            $totalInduc += $consumo_energia_reactiva[$t]->Induc;
                            $consumo_cap[$t] = $consumo_energia_reactiva[$t]->Capa;
                            $totalCapa += $consumo_energia_reactiva[$t]->Capa;
                        }else{
                            $consumo_induc[$t] = 0;
                            $totalInduc += 0;
                            $consumo_cap[$t] = 0;
                            $totalCapa += 0;
                        }
                    }

                    // foreach ($consumo_energia_reactiva as $consu) {
                    //     $eje2[] = $consu->eje;
                    //     $consumo_induc[] = $consu->Induc;
                    //     $totalInduc += $consu->Induc;
                    //     $consumo_cap[] = $consu->Capa;
                    //     $totalCapa += $consu->Capa;
                    // }
                }else{
                    $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa"))->where('date',$date_from)->groupBy('time')->get();
                    // dd($consumo_energia_activa);
                    if($contador2->tipo == 2)
                    {
                        $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Generacion Energia`) generacion_energia"))->where('date',$date_from)->groupBy('time')->get();
                        // dd($db_Generacion->toArray());
                        $t = 0;
                        foreach ($db_Generacion as $val)
                        {
                            if(isset($val->generacion_energia))
                            {
                                $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                            }else{
                                $generacion2[$t]['generacion_energia'] = 0;
                            }
                            $t++;
                        }
                        $db_Generacion = $generacion2;
                    }
                    // dd($db_Generacion);
                    // $consumo_energia_activa = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date = '".$date_from."' GROUP BY time");
                    foreach ($consumo_energia_activa as $consu) {
                        $eje[] = $consu->eje;
                        $consumo_activa[] = $consu->activa;
                        $totalActiva += $consu->activa;
                        if($aux_max_consumo <= $consu->activa)
                            $aux_max_consumo = $consu->activa;
                    }
                    $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date',$date_from)->groupBy('time')->get();
                    // dd($consumo_energia_reactiva);

                    foreach ($consumo_energia_reactiva as $consu) {
                        $eje2[] = $consu->eje;
                        $consumo_induc[] = $consu->Induc;
                        $totalInduc += $consu->Induc;
                        $consumo_cap[] = $consu->Capa;
                        $totalCapa += $consu->Capa;
                    }
                }
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';
                $number_days = 1;

                $consumo_energia_activa = $db->table('Consumo_Energia_Activa')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa"))->where('date',$date_from)->groupBy('time')->get();
                if($contador2->tipo == 2)
                {
                    $db_Generacion = $db->table('Generacion_Energia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Generacion Energia`) generacion_energia"))->where('date',$date_from)->groupBy('time')->get();
                }

                // $consumo_energia_activa = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Activa (kWh)`) activa FROM ".$contador.".`consumo_energia_activa` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($consumo_energia_activa as $consu) {
                    $eje[] = $consu->eje;
                    $consumo_activa[] = $consu->activa;
                    $totalActiva += $consu->activa;
                }
                $consumo_energia_reactiva = $db->table('Consumo_Energia_Reactiva')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa"))->where('date',$date_from)->groupBy('time')->get();

                // $consumo_energia_reactiva = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Energia Reactiva Inductiva (kVArh)`) Induc, SUM(`Energia Reactiva Capacitiva (kVArh)`) Capa FROM ".$contador.".`consumo_energia_reactiva` WHERE date = '".$date_from."' GROUP BY time");

                foreach ($consumo_energia_reactiva as $consu) {
                    $eje2[] = $consu->eje;
                    $consumo_induc[] = $consu->Induc;
                    $totalInduc += $consu->Induc;
                    $consumo_cap[] = $consu->Capa;
                    $totalCapa += $consu->Capa;
                }
                break;
        }
        $total_generacion = 0;
        if($contador2->tipo == 2)
        {
            $aux_generacion = $db_Generacion;
            foreach ($db_Generacion as $gen) {
                if($interval == 7 || $interval == 8 || $interval == 10 || $interval == 11 || $interval == 9)
                    $total_generacion = $total_generacion + $gen['generacion_energia'];
                else
                    $total_generacion = $total_generacion + $gen->generacion_energia;
            }
        }

        $dataPeriodo = [];
        $dataPeriodo['interval'] = $interval;
        $dataPeriodo['date_from'] = $date_from;
        $dataPeriodo['date_to'] = $date_to;
        $dataSubperiodo = $this->getInfoSubPeriodo($dataPeriodo);
        // dd($total_generacion,$aux_generacion);

        $user = Auth::user();//usuario logeado
        $titulo = 'Consumo de Energía';//Título del content
        $contador_label = $contador2->count_label;

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);

        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();


        \DB::disconnect('mysql2');
        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
            else
                $ctrl = 1;

            return view('consumo_energia.consumo_energia',compact('user','titulo','cliente','id','ctrl','eje','eje2','consumo_activa','consumo_cap','consumo_induc','label_intervalo','date_from','date_to','totalCapa','totalInduc','totalActiva','tipo_count','db_Generacion','total_generacion','contador_label','domicilio','dir_image_count','dates','aux_max_consumo','number_days','tipo_tarifa', 'dataSubperiodo'));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('consumo_energia.consumo_energia',compact('user','titulo','cliente','id','ctrl','eje','consumo_activa','consumo_cap','consumo_ind','label_intervalo','date_from','date_to','totalCapa','totalInduc','totalActiva','tipo_count','db_Generacion','total_generacion','contador_label','domicilio','dir_image_count','dates','number_days','tipo_tarifa'));
    }

    function AnalisisPotencia($id,Request $request)
    {
        $contador = strtolower(request()->input('contador'));
        $tipo_count = strtolower(request()->input('tipo'));
        if(empty($tipo_count))
        {
            $tipo_count = Count::where('user_id',$id)->first()->tipo;
            $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

        }
        $interval = Session::get('_flash')['intervalos'];
        $eje = array();
        $eje2 = array();
        $consumo_activa = array();
        $consumo_induc = array();
        $consumo_cap = array();
        $p_contratada = array();
        $p_demandada = array();
        $p_optima = array();
        $totalD = 0;
        $totalC = 0;
        $dates = array();
        $potencia_85_contratada = array();
        $potencia_105_contratada = array();
        $p_85_contratada = array();
        $p_105_contratada = array();

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        $aux_current_count = $aux_current_count[0]->label_current_count;
        if(!is_null($aux_current_count) || !empty($aux_current_count))
        {
            if(isset(Session::get('_flash')['current_count']) && !is_null(Session::get('_flash')['current_count']))
            {
                if(Session::get('_flash')['current_count'] != $aux_current_count)
                {
                    $flash['current_count'] = $aux_current_count;
                    $flash['intervalos'] = $interval;
                    Session::put('_flash',$flash);
                }
            }
        }

        if(!isset(Session::get('_flash')['current_count']))
        {
            if(empty($contador))
            {
                $contador2 = Count::where('user_id',$id)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }else{
            $current_count = Session::get('_flash')['current_count'];
            if(empty($contador))
            {
                $contador2 = Count::where('count_label',$current_count)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                // $potencia_demandada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                    $totalD += $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                    $totalC += $consu->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`Potencia Optima (kW)`) optima"))->where('date',$date_from)->groupBy('time')->get();
                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $potencia->optima;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                    $totalD += $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                // \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                    $totalC += $consu->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $potencia->optima;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                    $totalD += $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                    $totalC += $consu->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $potencia->optima;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }

                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $potencia_demandada = \DB::select("SELECT DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                    $totalD += $consu->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // \DB::select("SELECT DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                    $totalC += $consu->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $potencia->optima;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                    $totalD += $consu->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                    $totalC += $consu->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $potencia->optima;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $potencia_demandada = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($potencia_demandada as $consu) {
                //     // $eje[] = $consu->eje;
                //     $p_demandada[] = $consu->demandada;
                //     $totalD += $consu->demandada;
                // }
                for ($t=0; $t < 3; $t++) {
                    $p_demandada[$t] = 0;
                    $totalD += 0;
                    foreach ($potencia_demandada as $value) {
                        if($value->eje == $eje[$t])
                        {
                            $p_demandada[$t] = $value->demandada;
                            $totalD += $value->demandada;
                            break;
                        }
                    }
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $potencia_contratada = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                // foreach ($potencia_contratada as $consu) {
                //     // $eje2[] = $consu->eje;
                //     $p_contratada[] = $consu->contratada;
                //     $totalC += $consu->contratada;
                // }

                for ($t=0; $t < 3; $t++) {
                    $p_contratada[$t] = 0;
                    $totalC += 0;
                    foreach ($potencia_contratada as $value) {
                        if($value->eje == $eje[$t])
                        {
                            $p_contratada[$t] = $value->contratada;
                            $totalC += $value->contratada;
                            break;
                        }
                    }
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 3; $t++) {

                    $p_optima[$t] = 0;
                    foreach ($potencia_optima as $value) {
                        if($value->eje == $eje[$t])
                        {
                            $p_optima[$t] = $value->optima;
                            break;
                        }
                    }
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 3; $t++) {
                        $p_85_contratada[$t] = 0;
                        foreach ($potencia_85_contratada as $value) {
                            if($value->eje == $eje[$t])
                            {
                                $p_85_contratada[$t] = $value->contratada_ochenta;
                                break;
                            }
                        }
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    for ($t=0; $t < 3; $t++) {
                        $p_105_contratada[$t] = 0;
                        foreach ($potencia_105_contratada as $value) {
                            if($value->eje == $eje[$t])
                            {
                                $p_105_contratada[$t] = $value->contratada_ciento;
                                break;
                            }
                        }
                    }
                }
                break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $potencia_demandada = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                for ($t=0; $t < 3; $t++) {
                    if(isset($potencia_demandada[$t]->demandada))
                    {
                        $p_demandada[$t] = $potencia_demandada[$t]->demandada;
                        $totalD += $potencia_demandada[$t]->demandada;
                    }else{
                        $p_demandada[$t] = 0;
                        $totalD += 0;
                    }
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 3; $t++) {
                    if(isset($potencia_contratada[$t]->contratada))
                    {
                        $p_contratada[$t] = $potencia_contratada[$t]->contratada;
                        $totalC += $potencia_contratada[$t]->contratada;
                    }else{
                        $p_contratada[$t] = 0;
                        $totalC += 0;
                    }
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 3; $t++) {
                    if(isset($potencia_optima[$t]->optima))
                    {
                        $p_optima[$t] = $potencia_optima[$t]->optima;
                    }else{
                        $p_optima[$t] = 0;
                    }
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 3; $t++) {
                        if(isset($potencia_85_contratada[$t]->contratada_ochenta))
                        {
                            $p_85_contratada[$t] = $potencia_85_contratada[$t]->contratada_ochenta;
                        }else{
                            $p_85_contratada[$t] = 0;
                        }
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    for ($t=0; $t < 3; $t++) {
                        if(isset($potencia_105_contratada[$t]->contratada_ciento))
                        {
                            $p_105_contratada[$t] = $potencia_105_contratada[$t]->contratada_ciento;
                        }else{
                            $p_105_contratada[$t] = 0;
                        }
                    }
                }

                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                // $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                // $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->startOfYear()->year.")";
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                // foreach ($potencia_demandada as $consu) {
                //     $eje[] = $consu->eje;
                //     $p_demandada[] = $consu->demandada;
                //     $totalD += $consu->demandada;
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $p_demandada[$t] = 0;
                    $totalD += 0;
                    foreach ($potencia_demandada as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_demandada[$t] = $val->demandada;
                            $totalD += $val->demandada;
                            // $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                            break;
                        }
                    }
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                // foreach ($potencia_contratada as $consu) {
                //     $eje2[] = $consu->eje;
                //     $p_contratada[] = $consu->contratada;
                //     $totalC += $consu->contratada;
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $p_contratada[$t] = 0;
                    $totalC += 0;
                    foreach ($potencia_contratada as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_contratada[$t] = $val->contratada;
                            $totalC += $val->contratada;
                            // $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                            break;
                        }
                    }
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 12; $t++)
                {
                    $p_optima[$t] = 0;
                    foreach ($potencia_optima as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_optima[$t] = $val->optima;
                            break;
                        }
                    }
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 12; $t++)
                    {
                        $p_85_contratada[$t] = 0;
                        foreach ($potencia_85_contratada as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $p_85_contratada[$t] = $val->contratada_ochenta;
                                break;
                            }
                        }
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 12; $t++)
                    {
                        $p_105_contratada[$t] = 0;
                        foreach ($potencia_105_contratada as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $p_105_contratada[$t] = $val->contratada_ciento;
                                break;
                            }
                        }
                    }
                }

                break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                // foreach ($potencia_demandada as $consu) {
                //     $eje[] = $consu->eje;
                //     $p_demandada[] = $consu->demandada;
                //     $totalD += $consu->demandada;
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $p_demandada[$t] = 0;
                    $totalD += 0;
                    foreach ($potencia_demandada as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_demandada[$t] = $val->demandada;
                            $totalD += $val->demandada;
                            // $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                            break;
                        }
                    }
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                // foreach ($potencia_contratada as $consu) {
                //     $eje2[] = $consu->eje;
                //     $p_contratada[] = $consu->contratada;
                //     $totalC += $consu->contratada;
                // }
                // dd($potencia_contratada);
                for ($t=0; $t < 12; $t++)
                {
                    $p_contratada[$t] = 0;
                    $totalC += 0;
                    foreach ($potencia_contratada as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_contratada[$t] = $val->contratada;
                            $totalC += $val->contratada;
                            // $generacion2[$t]['generacion_energia'] = $val->generacion_energia;
                            break;
                        }
                    }
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 12; $t++)
                {
                    $p_optima[$t] = 0;
                    foreach ($potencia_optima as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $p_optima[$t] = $val->optima;
                            break;
                        }
                    }
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 12; $t++)
                    {
                        $p_85_contratada[$t] = 0;
                        foreach ($potencia_85_contratada as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $p_85_contratada[$t] = $val->contratada_ochenta;
                                break;
                            }
                        }
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    for ($t=0; $t < 12; $t++)
                    {
                        $p_105_contratada[$t] = 0;
                        foreach ($potencia_105_contratada as $val)
                        {
                            if($val->eje == $eje[$t])
                            {
                                $p_105_contratada[$t] = $val->contratada_ciento;
                                break;
                            }
                        }
                    }
                }


                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                $dates = [];
                $date_from_Car = \Carbon\Carbon::parse($date_from);
                $date_to_Car = \Carbon\Carbon::parse($date_to);

                if($date_to != $date_from)
                {
                    for($date = $date_from_Car; $date->lte($date_to_Car); $date->addDay()) {
                        $dates[] = $date->format('Y-m-d');
                    }

                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                    // foreach ($potencia_demandada as $analisis) {
                    //     $eje[] = $analisis->eje;
                    //     $p_demandada[] = $analisis->demandada;
                    //     $totalD += $analisis->demandada;
                    // }
                    for ($t=0; $t < count($dates); $t++)
                    {
                        if(isset($potencia_demandada[$t]->demandada))
                        {
                            $p_demandada[$t] = $potencia_demandada[$t]->demandada;
                            $totalD += $potencia_demandada[$t]->demandada;
                        }else{
                            $p_demandada[$t] = 0;
                            $totalD += 0;
                        }
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                    // $potencia_contratada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                    // foreach ($potencia_contratada as $contratada) {
                    //     $eje2[] = $contratada->eje;
                    //     $p_contratada[] = $contratada->contratada;
                    //     $totalC += $contratada->contratada;
                    // }
                    for ($t=0; $t < count($dates); $t++)
                    {
                        if(isset($potencia_contratada[$t]->contratada))
                        {
                            $p_contratada[$t] = $potencia_contratada[$t]->contratada;
                            $totalC += $potencia_contratada[$t]->contratada;
                        }else{
                            $p_contratada[$t] = 0;
                            $totalC += 0;
                        }
                    }

                    $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,MAX(`Potencia Optima (kW)`) optima"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                    for ($t=0; $t < count($dates); $t++)
                    {
                        if(isset($potencia_optima[$t]->optima))
                        {
                            $p_optima[$t] = $potencia_optima[$t]->optima;
                        }else{
                            $p_optima[$t] = 0;
                        }
                    }

                    if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                    {
                        $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                        for ($t=0; $t < count($dates); $t++)
                        {
                            if(isset($potencia_85_contratada[$t]->contratada_ochenta))
                            {
                                $p_85_contratada[$t] = $potencia_85_contratada[$t]->contratada_ochenta;
                            }else{
                                $p_85_contratada[$t] = 0;
                            }
                        }

                        $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                        for ($t=0; $t < count($dates); $t++)
                        {
                            if(isset($potencia_105_contratada[$t]->contratada_ciento))
                            {
                                $p_105_contratada[$t] = $potencia_105_contratada[$t]->contratada_ciento;
                            }else{
                                $p_105_contratada[$t] = 0;
                            }
                        }
                    }
                }else{
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_demandada as $analisis) {
                        $eje[] = $analisis->eje;
                        $p_demandada[] = $analisis->demandada;
                        $totalD += $analisis->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                    // $potencia_contratada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                    foreach ($potencia_contratada as $contratada) {
                        $eje2[] = $contratada->eje;
                        $p_contratada[] = $contratada->contratada;
                        $totalC += $contratada->contratada;
                    }

                    $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Optima (kW)`) optima"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_optima as $potencia) {
                        $p_optima[] = $potencia->optima;
                    }

                    if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                    {
                        $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date',$date_from)->groupBy('time')->get();
                        foreach ($potencia_85_contratada as $potencia) {
                            $p_85_contratada[] = $potencia->contratada_ochenta;
                        }

                        $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date',$date_from)->groupBy('time')->get();
                        foreach ($potencia_105_contratada as $potencia) {
                            $p_105_contratada[] = $potencia->contratada_ciento;
                        }
                    }

                }

                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Hoy")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();
                foreach ($potencia_demandada as $analisis) {
                    $eje[] = $analisis->eje;
                    $p_demandada[] = $analisis->demandada;
                    $totalD += $analisis->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                // $potencia_contratada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($potencia_contratada as $contratada) {
                    $eje2[] = $contratada->eje;
                    $p_contratada[] = $contratada->contratada;
                    $totalC += $contratada->contratada;
                }

                $potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,MAX(`Potencia Optima (kW)`) optima"))->where('date',$date_from)->groupBy('time')->get();

                foreach ($potencia_optima as $potencia) {
                    $p_optima[] = $contratada->contratada;
                }

                if($tipo_tarifa == 2 || $tipo_tarifa == 3)
                {
                    $potencia_85_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`85% Potencia Contatratada (KW)`) contratada_ochenta"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_85_contratada as $potencia)
                    {
                        $p_85_contratada[] = $potencia->contratada_ochenta;
                    }

                    $potencia_105_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`105% Potencia Contatada (KW)`) contratada_ciento"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_105_contratada as $potencia)
                    {
                        $p_105_contratada[] = $potencia->contratada_ciento;
                    }
                }
                break;
        }

        $user = Auth::user();//usuario logeado
        $titulo = 'Análisis de Potencia';//Título del content

        $potencia_contratada = $db->table('Potencia_Contratada')->select(\DB::raw("Periodo columna, MAX(`Potencia_contratada`) potencia_contratada"))->where('date_start','<=',$date_from)->orWhere('date_end','>=',$date_to)->groupBy('Periodo')->get();
        // dd($potencia_contratada);

        // $potencia_table = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("Periodo columna, SUM(`Exceso De Potencia (kW)`) excesos, SUM(`Precio Potencia Contratada (kW)`) precio_potencia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get();

        //DETERMINACIÓN PERIODOS DE POTENCIA OPTIMA

        // $periodos_analisis_potencia_optima = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(Analisis_Potencia.time, '%H:%i') eje, Tarifa.Periodo"))->join('Tarifa',"Tarifa.hora_start","<=",\DB::raw("Analisis_Potencia.time AND Tarifa.Mes = MONTH(Analisis_Potencia.date) AND Tarifa.hora_end >= Analisis_Potencia.time AND Tarifa.Mes = MONTH(Analisis_Potencia.date)"))->where("Analisis_Potencia.date", '>=',$date_from)->where("Analisis_Potencia.date", '<=',$date_to)->orderBY('Analisis_Potencia.time')->get()->toArray();

        if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first())
        {
            $potencia_optima_tabla = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get();
            if($date_from == $date_to)
            {
                $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get();
            }else{
                $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,MAX(`Potencia_contratada`) p_optima"))->orderBy('Periodo')->get();
            }
        }else{
            if($tipo_tarifa == 1)
            {
                $potencia_optima[0]['p_optima'] = 0;
                $potencia_optima[1]['p_optima'] = 0;
                $potencia_optima[2]['p_optima'] = 0;
                $potencia_optima[3]['p_optima'] = 0;
                $potencia_optima[4]['p_optima'] = 0;
                $potencia_optima[5]['p_optima'] = 0;
            }else{
                $potencia_optima[0]['p_optima'] = 0;
                $potencia_optima[1]['p_optima'] = 0;
                $potencia_optima[2]['p_optima'] = 0;
            }
        }
        // dd($potencia_optima_tabla,$potencia_optima);

        // SENTENCIAS ANTES DE LOS CAMBIOS INDICADOS POR ANDREI
        $coste_exceso_potencia_actual_ = array();


        if($contador2->database == 'Prueba_Contador_6.0_V3')
        {
            $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
            $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
            $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
            $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
            $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
            $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
            $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();

            $coste_exceso_potencia_actual = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            foreach ($coste_exceso_potencia_actual[0] as $value)
            {
                $coste_exceso_potencia_actual_[] = $value;
            }

            $coste_termino_potencia_actual = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("(Coste_Exceso_Potencia.P1 + SUM(Coste_Potencia_Contratada.P1)) AS P1,(Coste_Exceso_Potencia.P2 + SUM(Coste_Potencia_Contratada.P2)) AS P2,(Coste_Exceso_Potencia.P3 + SUM(Coste_Potencia_Contratada.P3)) AS P3,(Coste_Exceso_Potencia.P4 + SUM(Coste_Potencia_Contratada.P4)) AS P4,(Coste_Exceso_Potencia.P5 + SUM(Coste_Potencia_Contratada.P5)) AS P5,(Coste_Exceso_Potencia.P6 + SUM(Coste_Potencia_Contratada.P6)) AS P6"))->leftJoin('Coste_Potencia_Contratada','Coste_Potencia_Contratada.date','>=',\DB::raw("Coste_Exceso_Potencia.date AND Coste_Potencia_Contratada.date <= Coste_Exceso_Potencia.date"))->get()->toArray();

            $coste_termino_potencia_actual_ = array();

            foreach ($coste_termino_potencia_actual[0] as $value)
            {
                $coste_termino_potencia_actual_[] = $value;
            }

            // dd($coste_termino_potencia_actual_);

            // dd($situacion_actual, $coste_exceso_potencia_actual_);

            $situacion_optima = $db->table('Situacion_Optima_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Potencia Contratada (€)`) coste_contratada, SUM(`Coste Exceso Potencia (€)`) coste_exceso, SUM(`Coste Termino Potencia (€)`) termino_potencia"))->where("date", '>=',$date_from)->where("date", '<=',$date_to)->groupBY('Periodo')->get()->toArray();

            $coste_exceso_potencia_optima = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            // dd($coste_exceso_potencia_optima);

            // SELECT ((1 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `P1`,((0.5 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `P2`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `P3`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `P4`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `P5`,((0.17 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `P6`,((1 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.5 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.17 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS Total from `ZPI_Dias_Excesos_y_Precio_Contratada_Optima` WHERE date BETWEEN "Variable_Fecha_1" AND  "Variable_Fecha_2"


            $coste_exceso_potencia_optima_ = array();

            foreach ($coste_exceso_potencia_optima[0] as $value)
            {
                $coste_exceso_potencia_optima_[] = $value;
            }

            $termino_potencia_optima = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("(Coste_Exceso_Potencia.P1 + SUM(Coste_Potencia_Contratada_Propuesta.P1)) AS P1,(Coste_Exceso_Potencia.P2 + SUM(Coste_Potencia_Contratada_Propuesta.P2)) AS P2,(Coste_Exceso_Potencia.P3 + SUM(Coste_Potencia_Contratada_Propuesta.P3)) AS P3,(Coste_Exceso_Potencia.P4 + SUM(Coste_Potencia_Contratada_Propuesta.P4)) AS P4,(Coste_Exceso_Potencia.P5 + SUM(Coste_Potencia_Contratada_Propuesta.P5)) AS P5,(Coste_Exceso_Potencia.P6 + SUM(Coste_Potencia_Contratada_Propuesta.P6)) AS P6"))->leftJoin('Coste_Potencia_Contratada_Propuesta','Coste_Potencia_Contratada_Propuesta.date','>=',\DB::raw("Coste_Exceso_Potencia.date AND Coste_Potencia_Contratada_Propuesta.date <= Coste_Exceso_Potencia.date"))->get()->toArray();

            $termino_potencia_optima_ = array();

            foreach ($termino_potencia_optima[0] as $value)
            {
                $termino_potencia_optima_[] = $value;
            }

        }elseif($contador2->tarifa == 1){
            $situacion_actual = $db->table('Situacion_Actual_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Potencia Contratada (€)`) coste_contratada, SUM(`Coste Exceso Potencia (€)`) coste_exceso, SUM(`Coste Termino Potencia (€)`) termino_potencia"))->where("date", '>=',$date_from)->where("date", '<=',$date_to)->groupBY('Periodo')->get()->toArray();

            $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
            $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
            $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
            $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
            $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
            $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
            $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();


            $coste_exceso_potencia_actual = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            foreach ($coste_exceso_potencia_actual[0] as $value)
            {
                $coste_exceso_potencia_actual_[] = $value;
            }
            $coste_termino_potencia_actual = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("(Coste_Exceso_Potencia.P1 + SUM(Coste_Potencia_Contratada.P1)) AS P1,(Coste_Exceso_Potencia.P2 + SUM(Coste_Potencia_Contratada.P2)) AS P2,(Coste_Exceso_Potencia.P3 + SUM(Coste_Potencia_Contratada.P3)) AS P3,(Coste_Exceso_Potencia.P4 + SUM(Coste_Potencia_Contratada.P4)) AS P4,(Coste_Exceso_Potencia.P5 + SUM(Coste_Potencia_Contratada.P5)) AS P5,(Coste_Exceso_Potencia.P6 + SUM(Coste_Potencia_Contratada.P6)) AS P6"))->leftJoin('Coste_Potencia_Contratada','Coste_Potencia_Contratada.date','>=',\DB::raw("Coste_Exceso_Potencia.date AND Coste_Potencia_Contratada.date <= Coste_Exceso_Potencia.date"))->get()->toArray();

            $coste_termino_potencia_actual_ = array();

            foreach ($coste_termino_potencia_actual[0] as $value)
            {
                $coste_termino_potencia_actual_[] = $value;
            }

            // dd($coste_termino_potencia_actual_);

            // dd($situacion_actual, $coste_exceso_potencia_actual_);

            $situacion_optima = $db->table('Situacion_Optima_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Potencia Contratada (€)`) coste_contratada, SUM(`Coste Exceso Potencia (€)`) coste_exceso, SUM(`Coste Termino Potencia (€)`) termino_potencia"))->where("date", '>=',$date_from)->where("date", '<=',$date_to)->groupBY('Periodo')->get()->toArray();

            $coste_exceso_potencia_optima = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            // dd($coste_exceso_potencia_optima);

            // SELECT ((1 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `P1`,((0.5 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `P2`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `P3`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `P4`,((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `P5`,((0.17 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `P6`,((1 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.5 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.37 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end))))+ ((0.17 * 1.4064) * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS Total from `ZPI_Dias_Excesos_y_Precio_Contratada_Optima` WHERE date BETWEEN "Variable_Fecha_1" AND  "Variable_Fecha_2"


            $coste_exceso_potencia_optima_ = array();

            foreach ($coste_exceso_potencia_optima[0] as $value)
            {
                $coste_exceso_potencia_optima_[] = $value;
            }

            $termino_potencia_optima = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("(Coste_Exceso_Potencia.P1 + SUM(Coste_Potencia_Contratada_Propuesta.P1)) AS P1,(Coste_Exceso_Potencia.P2 + SUM(Coste_Potencia_Contratada_Propuesta.P2)) AS P2,(Coste_Exceso_Potencia.P3 + SUM(Coste_Potencia_Contratada_Propuesta.P3)) AS P3,(Coste_Exceso_Potencia.P4 + SUM(Coste_Potencia_Contratada_Propuesta.P4)) AS P4,(Coste_Exceso_Potencia.P5 + SUM(Coste_Potencia_Contratada_Propuesta.P5)) AS P5,(Coste_Exceso_Potencia.P6 + SUM(Coste_Potencia_Contratada_Propuesta.P6)) AS P6"))->leftJoin('Coste_Potencia_Contratada_Propuesta','Coste_Potencia_Contratada_Propuesta.date','>=',\DB::raw("Coste_Exceso_Potencia.date AND Coste_Potencia_Contratada_Propuesta.date <= Coste_Exceso_Potencia.date"))->get()->toArray();

            $termino_potencia_optima_ = array();

            foreach ($termino_potencia_optima[0] as $value)
            {
                $termino_potencia_optima_[] = $value;
            }
        }elseif($contador2->tarifa == 2 || $contador2->tarifa == 3){

            // $termino_potencia_30 = \DB::select("select `ZPI_Contador_Festivos_Periodos`.`Periodo` AS `Periodo`,(case when (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 0.85) > MAX(`EAct imp(kWh)`*4) then (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 0.85) * `ZPI_Precio_Potencia_Contratada`.`Precio` when (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 0.85) <= MAX(`EAct imp(kWh)`*4) and (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 1.05) >= MAX(`EAct imp(kWh)`*4) then MAX(`EAct imp(kWh)`*4) * `ZPI_Precio_Potencia_Contratada`.`Precio` when (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 1.05) < MAX(`EAct imp(kWh)`*4) then ((((MAX(`EAct imp(kWh)`*4) - (`ZPI_Precio_Potencia_Contratada`.`Potencia_contratada` * 1.05)) * 2) + MAX(`EAct imp(kWh)`*4)) * `ZPI_Precio_Potencia_Contratada`.`Precio`) end) AS `Coste Termino Potencia (€)` from `ZPI_Contador_Festivos_Periodos` left join `ZPI_Precio_Potencia_Contratada` on `ZPI_Contador_Festivos_Periodos`.`date` between `ZPI_Precio_Potencia_Contratada`.`Potencia Contratada desde` and `ZPI_Precio_Potencia_Contratada`.`Potencia Contrada hasta` and `ZPI_Contador_Festivos_Periodos`.`date` between `ZPI_Precio_Potencia_Contratada`.`Precio potencia desde` and `ZPI_Precio_Potencia_Contratada`.`Precio potencia hasta` and `ZPI_Contador_Festivos_Periodos`.`Periodo` = `ZPI_Precio_Potencia_Contratada`.`Periodo` WHERE date BETWEEN ".$date_from." AND  ".$date_to." group by `ZPI_Contador_Festivos_Periodos`.`Periodo`");

            $coste_termino_potencia_actual = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            $coste_termino_potencia_actual_ = array();

            foreach ($coste_termino_potencia_actual[0] as $value)
            {
                $coste_termino_potencia_actual_[] = $value;
            }

            $termino_potencia_optima = $db->table('ZPI_Potencia_Maxima_Dia_Optima')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            $termino_potencia_optima_ = array();

            foreach ($termino_potencia_optima[0] as $value)
            {
                $termino_potencia_optima_[] = $value;
            }
        }




        // $comparador_ofertas_potencia = $db->table('ZPI_Dias_Periodo')->select(\DB::raw("ZPI_Dias_Periodo.Periodo eje, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia.P6 end) coste_potencia, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia_Propuesta.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia_Propuesta.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia_Propuesta.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia_Propuesta.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia_Propuesta.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia_Propuesta.P6 end) coste_potencia_propuesto, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then (Coste_Termino_Potencia.P1 - Coste_Termino_Potencia_Propuesta.P1) when (ZPI_Dias_Periodo.Periodo = 'P2') then (Coste_Termino_Potencia.P2 - Coste_Termino_Potencia_Propuesta.P2) when (ZPI_Dias_Periodo.Periodo = 'P3') then (Coste_Termino_Potencia.P3 - Coste_Termino_Potencia_Propuesta.P3) when (ZPI_Dias_Periodo.Periodo = 'P4') then (Coste_Termino_Potencia.P4 - Coste_Termino_Potencia_Propuesta.P4) when (ZPI_Dias_Periodo.Periodo = 'P5') then (Coste_Termino_Potencia.P5 - Coste_Termino_Potencia_Propuesta.P5) when (ZPI_Dias_Periodo.Periodo = 'P6') then (Coste_Termino_Potencia.P6 - Coste_Termino_Potencia_Propuesta.P6) end) diferencia"))->leftJoin('Coste_Termino_Potencia','ZPI_Dias_Periodo.date','>=',\DB::raw("Coste_Termino_Potencia.date_start AND ZPI_Dias_Periodo.date <= Coste_Termino_Potencia.date_end"))->leftJoin('Coste_Termino_Potencia_Propuesta','Coste_Termino_Potencia.date_start','=',\DB::raw("Coste_Termino_Potencia.date_start AND Coste_Termino_Potencia.date_end = Coste_Termino_Potencia_Propuesta.date_end"))->groupBy('ZPI_Dias_Periodo.Periodo')->get()->toArray();

        // dd('ACTUAL','Situacion Actual (vista anterior)',$situacion_actual,'Sentencias Nuevas','* Coste Exceso Potencia:',$coste_exceso_potencia_actual_,'Termino Potencia',$coste_termino_potencia_actual,'OPTIMA','Situacion Optima (vista anterior)',$situacion_optima,'Sentencias Nuevas','* Coste Exceso Potencia: ',$coste_exceso_potencia_optima_,'Termino Potencia: ',$termino_potencia_optima_,'COMPARADOR DE OFERTAS POTENCIA');

        // dd($coste_termino_potencia_optima_);
        // dd($situacion_actual, $situacion_optima);
        // dd($situacion_actual);


        // \DB::select("SELECT Periodo columna, SUM(`Exceso De Potencia (kW)`) excesos, SUM(`P WHERE date >= '".$date_from."' AND '".$date_to."' >= date GROUP BY Periodo");
        $contador_label = $contador2->count_label;
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();

        \DB::disconnect('mysql2');
        $demanda_maxima = 0;
        $contratada_maxima = 0;
        foreach ($p_demandada as $value) {
            if($value >= $demanda_maxima)
            {
                $demanda_maxima = $value;
            }
        }
        foreach ($p_contratada as $value2) {
            if($value2 >= $contratada_maxima)
            {
                $contratada_maxima = $value2;
            }
        }
        $maxima_potencia = $demanda_maxima;
        // if($demanda_maxima >= $contratada_maxima)
        // {
        //     $maxima_potencia = $demanda_maxima;
        // }else{
        //     $maxima_potencia = $contratada_maxima;
        // }

        $date_to_Car = \Carbon\Carbon::createFromFormat("Y-m-d", $date_to);
        $date_from_Car = \Carbon\Carbon::createFromFormat("Y-m-d", $date_from);
        $diff_dates = $date_to_Car->diff($date_from_Car);

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
                else
                    $ctrl = 1;

                    return view('analisis_potencia.analisis_potencia',compact('user','titulo','cliente','id','ctrl','eje','eje2','consumo_activa','consumo_cap','consumo_induc','label_intervalo','p_demandada', 'p_contratada', 'p_optima', 'potencia_contratada','potencia_table','date_from','date_to','totalC','totalD','maxima_potencia','tipo_count','contador_label','domicilio','periodos_analisis_potencia_optima','potencia_optima','situacion_actual','situacion_optima','dir_image_count','potencia_optima_tabla','dates', 'diff_dates','coste_exceso_potencia_actual_','coste_termino_potencia_actual_','coste_exceso_potencia_optima_','coste_termino_potencia_optima_','termino_potencia_optima_','tipo_tarifa','p_105_contratada','p_85_contratada'));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('analisis_potencia.analisis_potencia',compact('user','titulo','cliente','id','ctrl','eje','consumo_activa','consumo_cap','consumo_ind','label_intervalo','p_demandada', 'p_contratada', 'p_optima','potencia_contratada','date_from','date_to','totalC','totalD','maxima_potencia','tipo_count','contador_label','domicilio','periodos_analisis_potencia_optima','potencia_optima','situacion_actual','situacion_optima','potencia_optima_tabla','dates', 'diff_dates','coste_exceso_potencia_actual_','coste_termino_potencia_actual_','coste_exceso_potencia_optima_','coste_termino_potencia_optima_','tipo_tarifa','p_105_contratada','p_85_contratada'));
    }

    function OptimizarPotencia(Request $request)
    {

        $sesion = $request->session()->all();
        $flash = $sesion['_flash'];

        $contador = Count::where('count_label',request()->input('cl'))->first();
        Session::put('_flash',$flash);
        $tipo_tarifa = $contador->tarifa;

        if($tipo_tarifa == 1)
        {
            $optimizer_route = base_path()."/optimization.py";
            $optimizer_route .= " -s ".$contador->host;
            $optimizer_route .= " -p ".$contador->port;
            $optimizer_route .= " -u ".$contador->username;
            $optimizer_route .= " -k ".$contador->password;
            $optimizer_route .= " -d ".$contador->database;
            $optimizer_route .= " -b ".$request->input("date_from");
            $optimizer_route .= " -e ".$request->input("date_to");
            $process = new Process($optimizer_route);
            $process->run();
            //die($process->getOutput());

            if (!$process->isSuccessful()) {
                try
                {
                    die($process->getOutput());
                    $message_process = $process->getOutput();
                    $data = json_decode($message_process);
                    $msg_error = "Command: ". $optimizer_route." \nResponse: ".$data->msg_error;
                    Log::info($msg_error);

                }
                catch(Exception $error)
                {

                }

            }
        }
        else if($tipo_tarifa == 2 || $tipo_tarifa == 3)
        {
            $optimizer_route = base_path()."/optimization_3.py";
            $optimizer_route .= " -s ".$contador->host;
            $optimizer_route .= " -p ".$contador->port;
            $optimizer_route .= " -u ".$contador->username;
            $optimizer_route .= " -k ".$contador->password;
            $optimizer_route .= " -d ".$contador->database;
            $optimizer_route .= " -b ".$request->input("date_from");
            $optimizer_route .= " -e ".$request->input("date_to");
            $optimizer_route .= " -o ".$tipo_tarifa;
            $process = new Process($optimizer_route);
            $process->run();
            //die($process->getOutput());

            if (!$process->isSuccessful()) {
                try
                {
                    die($process->getOutput());
                    $message_process = $process->getOutput();
                    $data = json_decode($message_process);
                    $msg_error = "Command: ". $optimizer_route." \nResponse: ".$data->msg_error;
                    Log::info($msg_error);

                }
                catch(Exception $error)
                {

                }

            }
        }
        return \Redirect::back();
    }

    function EnvioEmailOptimizacion(Request $request){

        $sesion = $request->session()->all();
        $flash = $sesion['_flash'];
        $contador = Count::where('count_label',$flash['current_count'])->first();
        Session::put('_flash',$flash);

        if($contador != null)
        {
            config(['database.connections.mysql2.host' => $contador->host]);
            config(['database.connections.mysql2.port' => $contador->port]);
            config(['database.connections.mysql2.database' => $contador->database]);
            config(['database.connections.mysql2.username' => $contador->username]);
            config(['database.connections.mysql2.password' => $contador->password]);
            env('MYSQL2_HOST',$contador->host);
            env('MYSQL2_DATABASE',$contador->database);
            env('MYSQL2_USERNAME', $contador->username);
            env('MYSQL2_PASSWORD',$contador->password);

            try {
                \DB::connection('mysql2')->getPdo();
            } catch (\Exception $e) {
                Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
                return \Redirect::back();
            }
            $db = \DB::connection('mysql2');

            $data_client = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` AS denominacion_social, `CUPS` AS cups'))->first();

            $backup = Mail::getSwiftMailer();


            $transport = new \Swift_SmtpTransport('smtp.1and1.es', 587, 'tls');
            $transport->setUsername('solicitudes-submeter@3seficiencia.com');
            $transport->setPassword('ASM948$$eficiencia#2017');


            $smtp = new Swift_Mailer($transport);

            $mail = new OptimizationRequest($data_client->denominacion_social, $contador->database, $data_client->cups);

            Mail::setSwiftMailer($smtp);

            Mail::to("info@3seficiencia.com")->send($mail);


            Mail::setSwiftMailer($backup);

            \DB::disconnect('mysql2');
        }
        return \Redirect::back();
    }

    function ComparadorOfertas($id,Request $request)
    {
        $contador = strtolower(request()->input('contador'));
        $eje = array();
        $eje2 = array();
        $consumo_activa = array();
        $coste_propuesto_energia = array();
        $consumo_induc = array();
        $consumo_cap = array();
        $p_contratada = array();
        $p_demandada = array();
        $coste_actual_potencia = array();
        $coste_propuesto_potencia = array();

        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);

        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                    // $potencia_demandada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                    // $potencia_contratada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }

                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }

                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                // dd($date_from, $date_to, Session::get('_flash')['date_from_personalice']);
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                // if($now == 1 || $now == 2 || $now == 3)
                // {
                //     // $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                //     // $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                //     if($dont == 0)
                //     {
                //         $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                //         $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                //     }
                // }elseif($now == 4 || $now == 7 || $now == 10){
                //     // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                //     // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                //     if($dont == 0)
                //     {
                //         $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                //         $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                //     }
                // }elseif($now == 5 || $now == 8 || $now == 11){
                //     // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                //     // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                //     if($dont == 0)
                //     {
                //         $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                //         $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                //     }
                // }elseif($now == 6 || $now == 9 || $now == 12){
                //     // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                //     // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                //     if($dont == 0)
                //     {
                //         $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                //         $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                //     }
                // }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
                break;
            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        // $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
            break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $number_days = 1;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        // $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
            break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                    foreach ($potencia_demandada as $analisis) {
                        $eje[] = $analisis->eje;
                        $p_demandada[] = $analisis->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                    foreach ($potencia_contratada as $contratada) {
                        $eje2[] = $contratada->eje;
                        $p_contratada[] = $contratada->contratada;
                    }
                }else{

                }
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                    foreach ($potencia_demandada as $analisis) {
                        $eje[] = $analisis->eje;
                        $p_demandada[] = $analisis->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_contratada as $contratada) {
                        $eje2[] = $contratada->eje;
                        $p_contratada[] = $contratada->contratada;
                    }
                }else{

                }
            break;
        }

        if($tipo_count < 3)
        {
            $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get();
            $precio_potencia_propuesta = $db->table('Precio_Potencia_Propuesta')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_potencia_propuesta"))->groupBy('Periodo')->get();

            $precio_energia = $db->table('Precio_Energia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_energia"))->groupBy('Periodo')->get();
            $precio_energia_propuesta = $db->table('Precio_Energia_Propuesta')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_energia_propuesta"))->groupBy('Periodo')->get();
            // dd($precio_energia,$precio_potencia,$precio_energia_propuesta, $precio_potencia_propuesta);


            if($tipo_tarifa == 2 || $tipo_tarifa == 3)
            {
                $coste_actual_energia_aux = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1,SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++) {
                    $coste_actual_energia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_actual_energia_aux[0] as $value) {
                    if(!is_null($value))
                    {
                        $coste_actual_energia[$h] = $value;
                    }
                    $h++;
                }
                // dd($coste_actual_energia);
                $coste_propuesto_energia_aux = $db->table('Coste_Termino_Energia_Propuesta')->select(\DB::raw("SUM(P1) costeP1,SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++) {
                    $coste_propuesto_energia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_propuesto_energia_aux[0] as $value) {
                    if(!is_null($value))
                    {
                        $coste_propuesto_energia[$h] = $value;
                    }
                    $h++;
                }
                // dd($coste_propuesto_energia);

            }else{
                $coste_actual_energia = $db->table('Comparador_De_Ofertas_Energia')->select(\DB::raw("Periodo, SUM(`Coste Actual (€)`) coste_energia, SUM(`Coste Propuesto (€)`) coste_energia_propuesto, SUM(`Diferencia (€)`) diferencia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
            }
            // dd($coste_actual_energia,$coste_propuesto_energia);
            $total_actual_energia = 0;
            $total_propuesto_energia = 0;
            $total_diferencia_energia = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_actual_energia as $value) {
                    $total_actual_energia += $value->coste_energia;
                    $total_propuesto_energia += $value->coste_energia_propuesto;
                    $total_diferencia_energia += $value->diferencia;
                }
            }else{
                $it = 1;
                foreach ($coste_actual_energia as $value) {
                    $total_actual_energia += $value;
                    $total_propuesto_energia += $coste_propuesto_energia[$it-1];
                    $total_diferencia_energia += $value-$coste_propuesto_energia[$it-1];
                    $it++;
                }
            }

            if($contador2->database == 'Prueba_Contador_6.0_V3')
            {
                // $coste_actual_potencia = $db->table('Comparador_De_Ofertas_Potencia')->select(\DB::raw("Periodo eje, `Coste Actual (€)` coste_potencia, `Coste Propuesto (€)` coste_potencia_propuesto, `Diferencia (€)` diferencia"))->where('Periodo','<>',NULL)->groupBy('Periodo')->get();

                $coste_actual_potencia = $db->table('ZPI_Dias_Periodo')->select(\DB::raw("ZPI_Dias_Periodo.Periodo eje, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia.P6 end) coste_potencia, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia_Propuesta.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia_Propuesta.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia_Propuesta.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia_Propuesta.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia_Propuesta.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia_Propuesta.P6 end) coste_potencia_propuesto, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then (Coste_Termino_Potencia.P1 - Coste_Termino_Potencia_Propuesta.P1) when (ZPI_Dias_Periodo.Periodo = 'P2') then (Coste_Termino_Potencia.P2 - Coste_Termino_Potencia_Propuesta.P2) when (ZPI_Dias_Periodo.Periodo = 'P3') then (Coste_Termino_Potencia.P3 - Coste_Termino_Potencia_Propuesta.P3) when (ZPI_Dias_Periodo.Periodo = 'P4') then (Coste_Termino_Potencia.P4 - Coste_Termino_Potencia_Propuesta.P4) when (ZPI_Dias_Periodo.Periodo = 'P5') then (Coste_Termino_Potencia.P5 - Coste_Termino_Potencia_Propuesta.P5) when (ZPI_Dias_Periodo.Periodo = 'P6') then (Coste_Termino_Potencia.P6 - Coste_Termino_Potencia_Propuesta.P6) end) diferencia"))->leftJoin('Coste_Termino_Potencia','ZPI_Dias_Periodo.date','>=',\DB::raw("Coste_Termino_Potencia.date_start AND ZPI_Dias_Periodo.date <= Coste_Termino_Potencia.date_end"))->leftJoin('Coste_Termino_Potencia_Propuesta','Coste_Termino_Potencia.date_start','=',\DB::raw("Coste_Termino_Potencia.date_start AND Coste_Termino_Potencia.date_end = Coste_Termino_Potencia_Propuesta.date_end"))->groupBy('ZPI_Dias_Periodo.Periodo')->get();
                // dd($coste_actual_potencia, $coste_actual_potencia2);

            }elseif($tipo_count < 3 && $tipo_tarifa == 1){
                $coste_actual_potencia = $db->table('Comparador_De_Ofertas_Potencia')->select(\DB::raw("Periodo eje, SUM(`Coste Actual (€)`) coste_potencia, SUM(`Coste Propuesto (€)`) coste_potencia_propuesto, SUM(`Diferencia (€)`) diferencia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->where('Periodo','<>',NULL)->groupBy('Periodo')->get();
            }else{
                $coste_actual_potencia_aux = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++)
                {
                    $coste_actual_potencia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_actual_potencia_aux[0] as $value) {
                    if(!is_null($value))
                        $coste_actual_potencia[$h] = $value;
                    $h++;
                }
                // dd($coste_actual_potencia, $coste_actual_potencia_aux);

                // $coste_propuesto_potencia_aux = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                $coste_propuesto_potencia_aux = $db->table('ZPI_Potencia_Maxima_Dia_Propuesta')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                for ($h=0; $h < 3; $h++)
                {
                    $coste_propuesto_potencia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_propuesto_potencia_aux[0] as $value) {
                    if(!is_null($value))
                        $coste_propuesto_potencia[$h] = $value;
                    $h++;
                }

            }

            $total_actual_potencia = 0;
            $total_propuesto_potencia = 0;
            $total_diferencia_potencia = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_actual_potencia as $value) {
                    $total_actual_potencia += $value->coste_potencia;
                    $total_propuesto_potencia += $value->coste_potencia_propuesto;
                    $total_diferencia_potencia += $value->diferencia;
                }
            }else{
                $it = 0;
                foreach ($coste_actual_potencia as $value) {
                    $total_actual_potencia += $value;
                    $total_propuesto_potencia += $coste_propuesto_potencia[$it];
                    $total_diferencia_potencia += $value-$coste_propuesto_potencia[$it];
                    $it++;
                }
            }
        }else{

            $precio_fijo = $db->table('Precio')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $precio_variable = $db->table('Precio_variable')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
            $coste_termino_variable = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_variable_propuesto = $db->table('Coste_Termino_Variable_Propuesto')->select(\DB::raw("SUM(`Coste Termino Variable Propuesto (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo_propuesto = $db->table('Coste_Termino_Fijo_Propuesto')->select(\DB::raw("SUM(`Coste Termino Fijo Propuesto (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();
        }

        // dd($suma2);

        $user = Auth::user();//usuario logeado
        $titulo = 'Comparador de Ofertas';//Título del content
        $cont = $contador;
        $contador_label = $contador2->count_label;
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        // dd(Session::get('_flash')['current_count']);

        if(is_null($aux_current_count) || empty($aux_current_count)){
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        }else{
            // dd("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
        }

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
            else
                $ctrl = 1;

            if($tipo_count < 3)
            {
                return view('comparador_ofertas.comparador_ofertas',compact('user','titulo','cliente','id','ctrl','eje','eje2','consumo_activa','consumo_cap','consumo_induc','label_intervalo','p_demandada', 'p_contratada','precio_energia','precio_potencia','coste_actual_energia','coste_actual_potencia','cont','date_from','date_to','precio_potencia_propuesta','precio_energia_propuesta','total_actual_energia','total_propuesto_energia','total_diferencia_energia','total_actual_potencia','total_propuesto_potencia','total_diferencia_potencia','tipo_count','contador_label','domicilio','dir_image_count','tipo_tarifa','coste_propuesto_potencia','coste_propuesto_energia'));
            }else{
                $what = (\DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id));
                // if($what[0]->label_current_count == 'GN1')
                //     dd($what[0]->label_current_count);
                return view('Gas.comparador_ofertas',compact('user','titulo','cliente','id','ctrl','cont','date_from','date_to','tipo_count','contador_label','label_intervalo','precio_fijo','precio_variable','coste_termino_variable','coste_termino_variable_propuesto','coste_termino_fijo','coste_termino_fijo_propuesto','domicilio','dir_image_count','tipo_tarifa','coste_propuesto_potencia','coste_propuesto_energia', 'contador2'));
            }
        }
        return \Redirect::to('https://submeter.es/');
        // return view('comparador_ofertas.comparador_ofertas',compact('user','titulo','cliente','id','ctrl','eje','consumo_activa','consumo_cap','consumo_ind','label_intervalo','p_demandada', 'p_contratada','date_from','date_to','precio_potencia_propuesta','precio_energia_propuesta','tipo_count','contador_label','domicilio','dir_image_count','tipo_tarifa','coste_propuesto_potencia','coste_propuesto_energia','coste_actual_energia'));
    }

    function ComparadorOfertasPdf($id,Request $request){
        $contador = $request->conta;
        $eje = array();
        $eje2 = array();
        $consumo_activa = array();
        $consumo_induc = array();
        $consumo_cap = array();
        $p_contratada = array();
        $p_demandada = array();
        $coste_actual_potencia = array();
        $coste_propuesto_potencia = array();

        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                        if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 4 || $now == 7 || $now == 10){
                    // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    }
                }elseif($now == 5 || $now == 8 || $now == 11){
                    // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    }
                }elseif($now == 6 || $now == 9 || $now == 12){
                    // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    // $potencia_demandada = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                    foreach ($potencia_demandada as $consu) {
                        $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    // \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        // $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_demandada as $consu) {
                        // $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
            break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $number_days = 1;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_demandada as $consu) {
                        // $eje[] = $consu->eje;
                        $p_demandada[] = $consu->demandada;
                    }
                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                    foreach ($potencia_contratada as $consu) {
                        $eje2[] = $consu->eje;
                        $p_contratada[] = $consu->contratada;
                    }
                }else{

                }
            break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                    foreach ($potencia_demandada as $analisis) {
                        $eje[] = $analisis->eje;
                        $p_demandada[] = $analisis->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                    foreach ($potencia_contratada as $contratada) {
                        $eje2[] = $contratada->eje;
                        $p_contratada[] = $contratada->contratada;
                    }
                }
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';
                if($tipo_count < 3)
                {
                    $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                    foreach ($potencia_demandada as $analisis) {
                        $eje[] = $analisis->eje;
                        $p_demandada[] = $analisis->demandada;
                    }

                    $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();
                    foreach ($potencia_contratada as $contratada) {
                        $eje2[] = $contratada->eje;
                        $p_contratada[] = $contratada->contratada;
                    }
                }
                break;
        }
        if($tipo_count < 3)
        {
            $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get();
            $precio_potencia_propuesta = $db->table('Precio_Potencia_Propuesta')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_potencia_propuesta"))->groupBy('Periodo')->get();

            $precio_energia = $db->table('Precio_Energia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_energia"))->groupBy('Periodo')->get();
            $precio_energia_propuesta = $db->table('Precio_Energia_Propuesta')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_energia_propuesta"))->groupBy('Periodo')->get();
            // dd($precio_energia,$precio_potencia,$precio_energia_propuesta, $precio_potencia_propuesta);


            if($tipo_tarifa == 2 || $tipo_tarifa == 3)
            {
                $coste_actual_energia_aux = $db->table('Coste_Termino_Energia')->select(\DB::raw("SUM(P1) costeP1,SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++) {
                    $coste_actual_energia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_actual_energia_aux[0] as $value) {
                    if(!is_null($value))
                    {
                        $coste_actual_energia[$h] = $value;
                    }
                    $h++;
                }
                // dd($coste_actual_energia);
                $coste_propuesto_energia_aux = $db->table('Coste_Termino_Energia_Propuesta')->select(\DB::raw("SUM(P1) costeP1,SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++) {
                    $coste_propuesto_energia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_propuesto_energia_aux[0] as $value) {
                    if(!is_null($value))
                    {
                        $coste_propuesto_energia[$h] = $value;
                    }
                    $h++;
                }
                // dd($coste_actual_energia,$coste_propuesto_energia);
                // dd($coste_propuesto_energia);

            }else{
                $coste_actual_energia = $db->table('Comparador_De_Ofertas_Energia')->select(\DB::raw("Periodo, `Coste Actual (€)` coste_energia, `Coste Propuesto (€)` coste_energia_propuesto, `Diferencia (€)` diferencia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();
            }
            $total_actual_energia = 0;
            $total_propuesto_energia = 0;
            $total_diferencia_energia = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_actual_energia as $value) {
                    $total_actual_energia += $value->coste_energia;
                    $total_propuesto_energia += $value->coste_energia_propuesto;
                    $total_diferencia_energia += $value->diferencia;
                }
            }else{
                $it = 1;
                foreach ($coste_actual_energia as $value) {
                    $total_actual_energia += $value;
                    $total_propuesto_energia += $coste_propuesto_energia[$it-1];
                    $total_diferencia_energia += $value-$coste_propuesto_energia[$it-1];
                    $it++;
                }
            }

            if($contador2->database == 'Prueba_Contador_6.0_V3')
            {
                // $coste_actual_potencia = $db->table('Comparador_De_Ofertas_Potencia')->select(\DB::raw("Periodo eje, `Coste Actual (€)` coste_potencia, `Coste Propuesto (€)` coste_potencia_propuesto, `Diferencia (€)` diferencia"))->where('Periodo','<>',NULL)->groupBy('Periodo')->get();

                $coste_actual_potencia = $db->table('ZPI_Dias_Periodo')->select(\DB::raw("ZPI_Dias_Periodo.Periodo eje, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia.P6 end) coste_potencia, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then Coste_Termino_Potencia_Propuesta.P1 when (ZPI_Dias_Periodo.Periodo = 'P2') then Coste_Termino_Potencia_Propuesta.P2 when (ZPI_Dias_Periodo.Periodo = 'P3') then Coste_Termino_Potencia_Propuesta.P3 when (ZPI_Dias_Periodo.Periodo = 'P4') then Coste_Termino_Potencia_Propuesta.P4 when (ZPI_Dias_Periodo.Periodo = 'P5') then Coste_Termino_Potencia_Propuesta.P5 when (ZPI_Dias_Periodo.Periodo = 'P6') then Coste_Termino_Potencia_Propuesta.P6 end) coste_potencia_propuesto, (case when (ZPI_Dias_Periodo.Periodo = 'P1') then (Coste_Termino_Potencia.P1 - Coste_Termino_Potencia_Propuesta.P1) when (ZPI_Dias_Periodo.Periodo = 'P2') then (Coste_Termino_Potencia.P2 - Coste_Termino_Potencia_Propuesta.P2) when (ZPI_Dias_Periodo.Periodo = 'P3') then (Coste_Termino_Potencia.P3 - Coste_Termino_Potencia_Propuesta.P3) when (ZPI_Dias_Periodo.Periodo = 'P4') then (Coste_Termino_Potencia.P4 - Coste_Termino_Potencia_Propuesta.P4) when (ZPI_Dias_Periodo.Periodo = 'P5') then (Coste_Termino_Potencia.P5 - Coste_Termino_Potencia_Propuesta.P5) when (ZPI_Dias_Periodo.Periodo = 'P6') then (Coste_Termino_Potencia.P6 - Coste_Termino_Potencia_Propuesta.P6) end) diferencia"))->leftJoin('Coste_Termino_Potencia','ZPI_Dias_Periodo.date','>=',\DB::raw("Coste_Termino_Potencia.date_start AND ZPI_Dias_Periodo.date <= Coste_Termino_Potencia.date_end"))->leftJoin('Coste_Termino_Potencia_Propuesta','Coste_Termino_Potencia.date_start','=',\DB::raw("Coste_Termino_Potencia.date_start AND Coste_Termino_Potencia.date_end = Coste_Termino_Potencia_Propuesta.date_end"))->groupBy('ZPI_Dias_Periodo.Periodo')->get();
                // dd($coste_actual_potencia, $coste_actual_potencia2);

            }elseif($tipo_count < 3 && $tipo_tarifa == 1){
                $coste_actual_potencia = $db->table('Comparador_De_Ofertas_Potencia')->select(\DB::raw("Periodo eje, `Coste Actual (€)` coste_potencia, `Coste Propuesto (€)` coste_potencia_propuesto, `Diferencia (€)` diferencia"))->where('Periodo','<>',NULL)->groupBy('Periodo')->get();
            }else{
                $coste_actual_potencia_aux = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++)
                {
                    $coste_actual_potencia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_actual_potencia_aux[0] as $value) {
                    if(!is_null($value))
                        $coste_actual_potencia[$h] = $value;
                    $h++;
                }
                // dd($coste_actual_potencia);

                $coste_propuesto_potencia_aux = $db->table('ZPI_Potencia_Maxima_Dia_Propuesta')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio`*12/365 when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` and (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio`*12/365 when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`*12/365) ELSE '0' END))*COUNT(DISTINCT`date`) AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                for ($h=0; $h < 3; $h++)
                {
                    $coste_propuesto_potencia[$h] = 0;
                }
                $h = 0;
                foreach ($coste_propuesto_potencia_aux[0] as $value) {
                    if(!is_null($value))
                        $coste_propuesto_potencia[$h] = $value;
                    $h++;
                }

            }

            $total_actual_potencia = 0;
            $total_propuesto_potencia = 0;
            $total_diferencia_potencia = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_actual_potencia as $value) {
                    $total_actual_potencia += $value->coste_potencia;
                    $total_propuesto_potencia += $value->coste_potencia_propuesto;
                    $total_diferencia_potencia += $value->diferencia;
                }
            }else{
                $it = 0;
                foreach ($coste_actual_potencia as $value) {
                    $total_actual_potencia += $value;
                    $total_propuesto_potencia += $coste_propuesto_potencia[$it];
                    $total_diferencia_potencia += $value-$coste_propuesto_potencia[$it];
                    $it++;
                }
            }
        }else{
            $precio_fijo = $db->table('Precio')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $precio_variable = $db->table('Precio_variable')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
            $coste_termino_variable = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_variable_propuesto = $db->table('Coste_Termino_Variable_Propuesto')->select(\DB::raw("SUM(`Coste Termino Variable Propuesto (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo_propuesto = $db->table('Coste_Termino_Fijo_Propuesto')->select(\DB::raw("SUM(`Coste Termino Fijo Propuesto (€)`) coste"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();
        }
        // dd($precio_energia);
        $user = Auth::user();//usuario logeado
        $titulo = 'Comparador de Ofertas';//Título del content
        $cont = $contador;
        $contador_label = $contador2->count_label;
        $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        $nombreArchivoPdf = $titulo."_".$contador_label."_".$date_from."_".$date_to.".pdf";
        \DB::disconnect('mysql2');
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
            if(isset($dir_image_count))
            {
                if(!is_null($dir_image_count))
                    $image = $dir_image_count;
            }else{
                $image = "images/avatar.png";
            }

            if($tipo_count < 3)
            {
                $pdf = \PDF::loadView('comparador_ofertassss.comparador_pdf', compact('user','titulo','cliente','id','ctrl','eje','eje2','consumo_activa','consumo_cap','consumo_induc','label_intervalo','p_demandada', 'p_contratada','precio_energia','precio_potencia','coste_actual_energia','coste_actual_potencia','cont','date_from','date_to','precio_potencia_propuesta','precio_energia_propuesta','total_actual_energia','total_propuesto_energia','total_diferencia_energia','total_actual_potencia','total_propuesto_potencia','total_diferencia_potencia','image','cont','direccion','contador_label','dir_image_count','tipo_tarifa','coste_propuesto_potencia','coste_propuesto_energia'));
                $pdf->setPaper("A4", "portrait");
                return $pdf->download($nombreArchivoPdf);
            }else{
                $pdf = \PDF::loadView('Gas.comparador_ofertas_pdf', compact('user','titulo','cliente','id','ctrl','cont','date_from','date_to','tipo_count','contador_label','label_intervalo','precio_fijo','precio_variable','coste_termino_variable','coste_termino_variable_propuesto','coste_termino_fijo','coste_termino_fijo_propuesto','image','direccion','contador_label','dir_image_count','tipo_tarifa'));
                $pdf->setPaper("A4", "portrait");
                return $pdf->download($nombreArchivoPdf);
            }
        }
        return \Redirect::to('https://submeter.es/');
    }

    function ExportarComparacionOfertas($id)
    {
        $contador = strtolower(request()->input('contador'));
        $interval = Session::get('_flash')['intervalos'];
        $eje = array();
        $eje2 = array();
        $consumo_activa = array();
        $consumo_induc = array();
        $consumo_cap = array();
        $p_contratada = array();
        $p_demandada = array();

        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Ayer';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                // $potencia_demandada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();

                // $potencia_contratada = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date = '".$date_from."' GROUP BY time");
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Actual';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $potencia_demandada = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Anterior';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Actual';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $potencia_demandada = \DB::select("SELECT DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // \DB::select("SELECT DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");

                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Anterior';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DAY(date) eje, SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }elseif($now == 4 || $now == 7 || $now == 10){
                    $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                }elseif($now == 5 || $now == 8 || $now == 11){
                    $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                }elseif($now == 6 || $now == 9 || $now == 12){
                    $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                }
                $label_intervalo = 'Ultimo Trimestre';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // $potencia_demandada = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada FROM ".$contador.".`analisis_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $label_intervalo = 'Último Año';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                foreach ($potencia_demandada as $consu) {
                    $eje[] = $consu->eje;
                    $p_demandada[] = $consu->demandada;
                }
                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                foreach ($potencia_contratada as $consu) {
                    $eje2[] = $consu->eje;
                    $p_contratada[] = $consu->contratada;
                }
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                foreach ($potencia_demandada as $analisis) {
                    $eje[] = $analisis->eje;
                    $p_demandada[] = $analisis->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("date eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                foreach ($potencia_contratada as $contratada) {
                    $eje2[] = $contratada->eje;
                    $p_contratada[] = $contratada->contratada;
                }
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Hoy';

                $potencia_demandada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Demandada (kW)`) demandada"))->where('date',$date_from)->groupBy('time')->get();

                foreach ($potencia_demandada as $analisis) {
                    $eje[] = $analisis->eje;
                    $p_demandada[] = $analisis->demandada;
                }

                $potencia_contratada = $db->table('Analisis_Potencia')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje,SUM(`Potencia Contratada (kW)`) contratada"))->where('date',$date_from)->groupBy('time')->get();
                foreach ($potencia_contratada as $contratada) {
                    $eje2[] = $contratada->eje;
                    $p_contratada[] = $contratada->contratada;
                }
                break;
        }

        $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get();

        #$precio_potencia = \DB::select("SELECT Periodo eje, SUM(`Precio`) precio_potencia FROM ".$contador.".`precio_potencia` WHERE '".$date_from."' >= date_start AND '".$date_to."' <= date_end GROUP BY Periodo");

        $precio_energia = $db->table('Precio_Energia')->select(\DB::raw("Periodo eje, SUM(`Precio`) precio_energia"))->groupBy('Periodo')->get();

        // $precio_energia = \DB::select("SELECT Periodo eje, SUM(`Precio`) precio_energia FROM ".$contador.".`precio_energia` GROUP BY Periodo");

        // $suma = \DB::select("SELECT c.Periodo, SUM(c.activa) activa, SUM(c.reactiva) reactiva FROM (SELECT ".$contador.".coste_energia_activa.Periodo, ".$contador.".coste_energia_activa.`Coste Energia Activa (€)` activa, ".$contador.".coste_energia_reactiva.`Coste Energia Reactiva (€)`reactiva FROM ".$contador.".`coste_energia_activa` INNER JOIN ".$contador.".coste_energia_reactiva ON ".$contador.".coste_energia_reactiva.date = ".$contador.".coste_energia_activa.date AND ".$contador.".coste_energia_reactiva.Periodo = ".$contador.".coste_energia_activa.Periodo AND ".$contador.".coste_energia_reactiva.date >= '".$date_from."' AND ".$contador.".coste_energia_activa.date <= '".$date_to."') as c GROUP BY c.Periodo");

        $suma = $db->table('Coste_Termino_Energia')->select(\DB::raw("Periodo, SUM(`Coste Termino Energia (€)`) coste_energia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get();
        // dd($suma);
        // $suma = \DB::select("SELECT Periodo, SUM(`Coste Termino Energia (€)`) coste_energia FROM ".$contador.".`coste_termino_energia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Periodo");
        if($contador2->database == 'Prueba_Contador_6.0_V3')
        {
            // $suma2 = $db->table('Coste_Termino_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Termino Potencia (€)`) coste_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get();

            $suma2 = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("(Coste_Exceso_Potencia.P1 + SUM(Coste_Potencia_Contratada.P1)) AS P1,(Coste_Exceso_Potencia.P2 + SUM(Coste_Potencia_Contratada.P2)) AS P2,(Coste_Exceso_Potencia.P3 + SUM(Coste_Potencia_Contratada.P3)) AS P3,(Coste_Exceso_Potencia.P4 + SUM(Coste_Potencia_Contratada.P4)) AS P4,(Coste_Exceso_Potencia.P5 + SUM(Coste_Potencia_Contratada.P5)) AS P5,(Coste_Exceso_Potencia.P6 + SUM(Coste_Potencia_Contratada.P6)) AS P6"))->leftJoin('Coste_Potencia_Contratada','Coste_Potencia_Contratada.date','>=',\DB::raw("Coste_Exceso_Potencia.date_start AND Coste_Potencia_Contratada.date <= Coste_Exceso_Potencia.date_end"))->get();
            // dd($suma2,$suma22);

        }else{
            $suma2 = $db->table('Coste_Termino_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Termino Potencia (€)`) coste_potencia"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get();
        }

        // $suma2 = \DB::select("SELECT Periodo, SUM(`Coste Termino Potencia (€)`) coste_potencia FROM ".$contador.".`coste_termino_potencia` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Periodo");

        // dd($suma2);

        $user = Auth::user();//usuario logeado
        $titulo = 'Comparador de Ofertas';//Título del content
        $cont = $contador;
        \DB::disconnect('mysql2');

        $pdf = \PDF::loadView('comparador_ofertas.vista_comparador_ofertas',['user' => $user, 'tiulo' => $titulo, 'id' => $id, 'eje' => $eje, 'eje2' => $eje2, 'consumo_activa' => $consumo_activa, 'consumo_cap' => $consumo_cap, 'consumo_induc' => $consumo_induc,'label_intervalo' => $label_intervalo, 'p_demandada' => $p_demandada, 'p_contratada' => $p_contratada, 'precio_energia' => $precio_energia, 'precio_potencia' => $precio_potencia, 'suma' => $suma, 'suma2' => $suma2, 'contador' => $contador]);
        $pdf->setPaper("A4", "landscape");
        return $pdf->download("Comparador.pdf");
    }

    function CalculoComparadorOfertas(Request $request, $id){

        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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
        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        // dd($contador2);
        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');
        // dd($tipo_count);
        if($tipo_count < 3)
        {
            if($tipo_tarifa == 1)
            {
                for ($i=1; $i <=6 ; $i++) {
                    $db->table('Precio_Potencia_Propuesta')->where('Periodo','P'.$i)->update(['Precio' => (floatval(str_replace(',', '.', $request['potencia'.$i])))]);
                    $db->table('Precio_Energia_Propuesta')->where('Periodo','P'.$i)->update(['Precio' => (floatval(str_replace(',', '.', $request['energia'.$i])))]);
                }
            }else{
                for ($i=1; $i <= 3 ; $i++) {
                    $db->table('Precio_Potencia_Propuesta')->where('Periodo','P'.$i)->update(['Precio' => (floatval(str_replace(',', '.', $request['potencia'.$i])))]);
                    $db->table('Precio_Energia_Propuesta')->where('Periodo','P'.$i)->update(['Precio' => (floatval(str_replace(',', '.', $request['energia'.$i])))]);
                }
            }
        }else{
            // dd($request['precio_fijo_propuesto'],$request['precio_variable_propuesto']);

            $db->table('Precio')->where('date_start','<=',$request->date_from)->where('date_end','>=',$request->date_to)->update(['Precio_propuesto' => (floatval(str_replace(',', '.', $request['precio_fijo_propuesto'])))]);

            $db->table('Precio_variable')->where('date_start','<=',$request->date_from)->where('date_end','>=',$request->date_to)->update(['Precio_propuesto' => (floatval(str_replace(',', '.', $request['precio_variable_propuesto'])))]);
        }

        \DB::disconnect('mysql2');

        return redirect()->back();
    }

    function EmisionesCO2($id,Request $request)
    {
        $contador = (request()->input('contador'));
        $tipo_count = strtolower(request()->input('tipo'));
        if(empty($tipo_count))
        {
            $tipo_count = Count::where('user_id',$id)->first()->tipo;
            $tipo_tarifa = Count::where('user_id',$id)->first()->tarifa;

        }
        $interval = Session::get('_flash')['intervalos'];
        $emisiones = array();
        $eje = array();
        $emisiones2 = array();
        $dates = array();
        // if(empty($contador))
        // {
        //     $contador2 = Count::where('user_id',$id)->first();

        // }else{
        //     $contador2 = Count::where('count_label',$contador)->first();
        // }
        // dd(Session::get('_flash')['current_count']);

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        $aux_current_count = $aux_current_count[0]->label_current_count;
        if(!is_null($aux_current_count) || !empty($aux_current_count))
        {
            if(isset(Session::get('_flash')['current_count']) && !is_null(Session::get('_flash')['current_count']))
            {
                if(Session::get('_flash')['current_count'] != $aux_current_count)
                {
                    $flash['current_count'] = $aux_current_count;
                    $flash['intervalos'] = $interval;
                    Session::put('_flash',$flash);
                }
            }
        }
        // dd(Session::get('_flash')['current_count']);

        if(!isset(Session::get('_flash')['current_count']))
        {
            if(empty($contador))
            {
                $contador2 = Count::where('user_id',$id)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }else{
            $current_count = Session::get('_flash')['current_count'];

            if(empty($contador))
            {
                $contador2 = Count::where('count_label',$current_count)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;

            }else{
                $contador2 = Count::where('count_label',$contador)->first();
                $sesion = $request->session()->all();
                $flash = $sesion['_flash'];
                $flash['current_count'] = $contador2->count_label;
                Session::put('_flash',$flash);
                $url = Session::get('_previous')['url'];

                $current_count = Session::get('_flash')['current_count'];
                $tipo_count = $contador2->tipo;
                $tipo_tarifa = $contador2->tarifa;
            }
        }
        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date',$date_from)->groupBy('time')->get();

                // $emisiones = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date = '".$date_from."' GROUP BY time");
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $emisiones = \DB::select("SELECT (CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("DAY(date) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();

                // $emisiones = \DB::select("SELECT DAY(date) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY date ORDER BY date ASC");
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("DAY(date) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->orderBy('date','ASC')->get();
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 3; $t++) {
                    $emisiones2[$t] = 0;
                    foreach($emisiones as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $emisiones2[$t] = $val->emisiones;
                            break;
                        }
                    }
                }
                // $emisiones = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                $band = 0;
                for ($t=0; $t < 3; $t++) {
                    $emisiones2[$t] = 0;
                    foreach($emisiones as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $emisiones2[$t] = $val->emisiones;
                            break;
                        }
                    }
                }
                // dd($eje,$emisiones2);
                // $emisiones = \DB::select("SELECT (CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY MONTH(date) ORDER BY MONTH(date) ASC");
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                // $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                // $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                // for ($t=0; $t < 12; $t++) {
                //     if(isset($emisiones[$t]->emisiones) && $emisiones[$t]->eje == $eje[$t])
                //     {
                //         $emisiones2[$t] = $emisiones[$t]->emisiones;
                //     }else{
                //         $emisiones2[$t] = 0;
                //     }
                // }
                for ($t=0; $t < 12; $t++)
                {
                    $emisiones2[$t] = 0;
                    foreach ($emisiones as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $emisiones2[$t] = $val->emisiones;
                            break;
                        }
                    }
                }
                break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                // $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                // $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->startOfYear()->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje,SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                for ($t=0; $t < 12; $t++)
                {
                    $emisiones2[$t] = 0;
                    foreach ($emisiones as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $emisiones2[$t] = $val->emisiones;
                            break;
                        }
                    }
                }
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $date_from_Car = \Carbon\Carbon::parse($date_from);
                $date_to_Car = \Carbon\Carbon::parse($date_to);
                $label_intervalo = 'Personalizado';
                for($date = $date_from_Car; $date->lte($date_to_Car); $date->addDay())
                {
                    $dates[] = $date->format('Y-m-d');
                }
                $eje = $dates;

                if($date_from != $date_to)
                {
                    $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("date eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                }else{
                    $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date',$date_from)->groupBy('time')->get();
                }
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Hoy")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';

                $emisiones = $db->table('Emisiones_CO2')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones"))->where('date',$date_from)->groupBy('time')->get();

                // $emisiones = \DB::select("SELECT DATE_FORMAT(time, '%H:%i') eje, SUM(`Emisiones CO2 (kg CO2 eq)`) emisiones FROM ".$contador.".`emisiones_co2` WHERE date = '".$date_from."' GROUP BY time");
                break;
        }

        $user = Auth::user();//usuario logeado
        $titulo = 'Emisiones CO2';//Título del content
        $contador_label = $contador2->count_label;
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
                else
                    $ctrl = 1;

                    if(is_null($user->_perfil))
                        $direccion = 'sin ubicación';
                        else
                            $direccion = $user->_perfil->direccion;

                            return view('emisiones_co2.emisiones_co2',compact('user','titulo','cliente','id','ctrl','emisiones','label_intervalo','date_from','date_to','direccion','tipo_count','contador_label','domicilio','dir_image_count','eje','emisiones2','dates','tipo_tarifa'));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('emisiones_co2.emisiones_co2',compact('user','titulo','cliente','id','ctrl','emisiones','label_intervalo','date_from','date_to','direccion','tipo_count','contador_label','domicilio','dir_image_count','tipo_tarifa'));
    }

    function SimulacionFactura($id,Request $request)
    {
        // id representa el id del usuario que se desea ver  y $ctrl el control que indica que
        // la vista mostrada viene del panel administrativo

        $contador = strtolower(request()->input('contador'));
        $array_coste_activa = array();
        $coste_activa = 0;
        $array_coste_reactiva = array();
        $coste_reactiva = 0;
        $array_potencia_contratada = array();
        $potencia_contratada = 0;
        $array_exceso_potencia = array();
        $exceso_potencia = 0;
        $array_impuesto = array();
        $impuesto = 0;
        $array_equipo = array();
        $equipo = 0;
        // variables
        $E_Activa = array();
        $MES = array();
        $precio_energia = array();
        $E_Reactiva = array();
        $potencia_demandada = array();
        $coste_potencia_contratada_max = array();
        $total1 = 0;

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        $interval = Session::get('_flash')['intervalos'];

        $titulo = 'Simulacion de Factura';

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && (Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre" || Session::get('_flash')['label_intervalo_navigation'] == "Último Trimestre"))
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }

                $label_intervalo = 'Último Trimestre';
                break;
            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 4 || $now == 5 || $now == 6){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 7 || $now == 8 || $now == 9){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 10 || $now == 11 || $now == 12){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Trimestre Actual';
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';
                break;
        }
        $fechaEmision = \Carbon\Carbon::parse($date_from);
        $fechaExpiracion = \Carbon\Carbon::parse($date_to);

        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
        if($contador2->tipo < 3)
        {
            for ($i=1; $i < 7 ; $i++) {
                $periodos2[] = 'P'.$i;
            }

            $MES = $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();

            // COSTE DE LA ENERGÍA ACTIVA
            $precio_energia = $db->table('Precio_Energia')->select('Periodo','precio')->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->orderBy('Periodo','ASC')->get()->toArray();
            if($tipo_tarifa == 1)
                $coste_energia = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            else
                $coste_energia = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_energia as $coste_ener) {
                    $aux_index = 'costeP';
                    $aux_coste_energia[$index][$aux_index.($index+1)] = $coste_ener->costeP1;
                    $aux_coste_energia[$index][$aux_index.($index+2)] = $coste_ener->costeP2;
                    $aux_coste_energia[$index][$aux_index.($index+3)] = $coste_ener->costeP3;
                    $aux_coste_energia[$index][$aux_index.($index+4)] = $coste_ener->costeP4;
                    $aux_coste_energia[$index][$aux_index.($index+5)] = $coste_ener->costeP5;
                    $aux_coste_energia[$index][$aux_index.($index+6)] = $coste_ener->costeP6;
                    $index++;
                }
            }else{
                foreach ($coste_energia as $coste_ener) {
                    $aux_index = 'costeP';
                    $aux_coste_energia[$index][$aux_index.($index+1)] = $coste_ener->costeP1;
                    $aux_coste_energia[$index][$aux_index.($index+2)] = $coste_ener->costeP2;
                    $aux_coste_energia[$index][$aux_index.($index+3)] = $coste_ener->costeP3;
                    $index++;
                }
            }


            $coste_energia = $aux_coste_energia;

            if($tipo_tarifa == 1)
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
            else
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                    $aux_coste_reactiva[$index][$aux_index.($index+4)] = $coste_reac->costeP4;
                    $aux_coste_reactiva[$index][$aux_index.($index+5)] = $coste_reac->costeP5;
                    $aux_coste_reactiva[$index][$aux_index.($index+6)] = $coste_reac->costeP6;
                    $index++;
                }
            }else{
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                    $index++;
                }
            }

            $coste_reactiva = $aux_coste_reactiva;
            // foreach ($coste_reactiva[0] as $value) {
            //     dd($value);
            //     # code...
            // }
            //CANTIDAD DE POTECIA CONSUMIDA EN KWH
            $potencia_demandada = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("Periodo, MAX(`Potencia Contratada (kW)`) potencia_demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

            // COSTE DE LA POTENCIA CONTRATADA
            $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo, Precio precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get()->toArray();

            if($tipo_tarifa == 1)
            {
                $coste_potencia_contratada = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            }
            else{
                // $coste_potencia_contratada = $db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('Max(`date`)','<=',$date_to)->get()->toArray();
                $coste_potencia_contratada = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                $coste_potencia_contratada_max = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("MAX(Potencia_Maxima) maxima_po, RIGHT(Periodo,1) as periodo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                $coste_max = array();
                for($i = 0; $i < count($coste_potencia_contratada); $i++)
                {
                    $max = new \stdClass();
                    $max->maxima_po = 0.0;
                    $max->periodo = $i + 1;
                    $coste_max[] = $max;
                }

                foreach($coste_potencia_contratada_max as $max_pot)
                {
                    $idx = intval($max_pot->periodo) - 1;
                    $coste_max[$idx] = $max_pot;
                }
                $coste_potencia_contratada_max = $coste_max;
                // dd($coste_potencia_contratada_max);
            }
            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_potencia_contratada as $coste_poten) {
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
                foreach ($coste_potencia_contratada as $coste_poten) {
                    $aux_index = 'costeP';
                    $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->P1;
                    $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->P2;
                    $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->P3;
                    $index++;
                }
            }
            $coste_potencia_contratada = $aux_coste_potencia;

            $dias_potencia_contratada = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("COUNT(*) dias"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get()->toArray();

            $j = 0;
            if(!empty($precio_potencia))
            {
                foreach ($precio_potencia as $pp) {
                    $precio_potencia[$j]->precio_potencia = ($pp->precio_potencia*12/365)*count($dias_potencia_contratada);
                    $j++;
                }
            }

            // EXCESOS DE POTENCIA
            if($contador2->database == 'Prueba_Contador_6.0_V3')
            {
                $ktep = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'Ktep')->first();
                $kiP1 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP1')->first();
                $kiP2 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP2')->first();
                $kiP3 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP3')->first();
                $kiP4 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP4')->first();
                $kiP5 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP5')->first();
                $kiP6 = $db->table('Coeficientes_Excesos')->where('Coeficiente', 'KiP6')->first();

                $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();
                $db_excesos = $db->table('ZPI_Dias_Excesos_y_Precio_Contratada')->select(\DB::raw("(('$kiP1->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P1') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP1`,(('$kiP2->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P2') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP2`,(('$kiP3->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P3') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP3`,(('$kiP4->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P4') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP4`,(('$kiP5->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P5') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP5`,(('$kiP6->valor_coeficiente' * '$ktep->valor_coeficiente') * sqrt(sum((case when (`Periodo` = 'P6') then `Exceso De Potencia (kW)` else 0 end)))) AS `costeP6`"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            }else{
                if($tipo_tarifa == 1)
                {
                    $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                    $index = 0;
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

                    $exceso_potencia = $aux_excesos;
                }
            }



            // foreach ($MES as $mes) {
            //     foreach ($periodos2 as $P)
            //     {
            $E_Activa = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("SUM(`Energia Activa (kWh)`) as Activa, `Energia Reactiva Inductiva (kVArh)` as Reactiva"))->where("date", '>=',$date_from)->where("date", '<=',$date_to)->groupBy('Periodo')->get()->toArray();
            //     }
            // }
            //         foreach ($E_Activa as $value) {
            //             dd($value->Activa);
            //         }
            // dd($E_Activa[0]->Activa,$coste_energia,$coste_potencia_contratada,$db_excesos);
            $aux = array();
            $i = 0;
            $total1 = 0;
            $total_ = 0;
            $total2 = 0;
            $total3 = 0;
            // dd($E_Activa);
            if(!empty($E_Activa))
            {
                foreach ($E_Activa as $val) {
                    // PARTE DE TERMINO ENERGÍA ACTIVA
                    if(isset($coste_energia[0]))
                    {
                        $totales_parciales_energiaAct[] = $coste_energia[0]['costeP'.($i+1)];
                    }else{
                        $totales_parciales_energiaAct[]=0;
                    }
                    $total1 = $total1 + $totales_parciales_energiaAct[$i];

                    if(isset($coste_reactiva[0]))
                    {
                        $totales_parciales_energiaReact[] = $coste_reactiva[0]['costeP'.($i+1)];
                    }else{
                        $totales_parciales_energiaReact[]=0;
                    }
                    $total_ = $total_ + $totales_parciales_energiaReact[$i];

                    // PARTE DE ENERGÍA REACTIVA

                    // PARTE DE TÉRMINO DE POTENCIA

                    if($tipo_tarifa == 1)
                    {
                        if(isset($precio_potencia[$i%6]) && !empty($precio_potencia))
                        {
                            $totales_parciales_potencia[] = floatval($potencia_demandada[$i%6]->potencia_demandada)*floatval($precio_potencia[$i%6]->precio_potencia);
                        }else{
                            $totales_parciales_potencia[] = 0;
                        }
                    }else{
                        $totales_parciales_potencia[]=0;
                    }
                    $total2 = $total2 + $totales_parciales_potencia[$i];

                    $i++;
                }
                // dd($exceso_potencia[0]);
            }else{
                $totales_parciales_energiaAct[] = 0;
                $total1= 0;
                $totales_parciales_potencia[]=0;
                $total2=0;
                $total3=0;
            }

            if($tipo_tarifa != 1){
                foreach ($coste_potencia_contratada[0] as $value) {
                    $totales_parciales_potencia[] = ($value);
                    $total2 = $total2 + $totales_parciales_potencia[$i];
                    $i++;
                }
            }

            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($exceso_potencia[0] as $exc) {
                    // TOTAL DE EXCESOS
                    $total3 = $total3 + ($exc);
                    $index++;
                }
            }


            if($contador2->iee == 3)
            {
                $aux_iee = 0;
            }elseif($contador2->iee == 2){
                $aux_iee = 0.15;
            }else{
                $aux_iee = 1;
            }


            $sumatoria = $total1 + $total2 + $total3 + $total_;
            $impuesto = $sumatoria*0.0511269632*$aux_iee;
            // if($tipo_tarifa == 1)
            // {
                $equipo = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
                if(!empty($equipo))
                {
                    foreach ($equipo as $value) {
                        $IVA = ($sumatoria + $impuesto + (floatval($value->valor)*($diasDiferencia+1)))*0.21;
                    }
                }else{
                    $IVA = ($sumatoria + $impuesto)*0.21;
                }
            // }else{
            //     $IVA = ($sumatoria + $impuesto)*0.21;
            // }
        }else{

            $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

            $precio_variable = $db->table('Precio_variable')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $descuento_variable = $db->table('Descuento_variable')->select(\DB::raw("Descuento, Descuento_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $precio_fijo = $db->table('Precio')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $descuento = $db->table('Descuento')->select(\DB::raw("Descuento, Descuento_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $coste_precio_fijo = $db->table('Coste_Precio_Fijo')->select(\DB::raw("SUM(`Coste Precio Fijo (€)`) Precio"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_descuento_fijo = $db->table('Coste_Descuento_Fijo')->select(\DB::raw("SUM(`Coste Descuento Fijo (€)`) Descuento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) Precio"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $consumo_GN_kWh_diario = $db->table('ZPI_GN_kWh_diario')->select(\DB::raw("SUM(`Qd Diaria (kWh)`) consumo"))->where('date',$date_from)->get();

            $I_E_HC = $db->table('Impuesto_HC')->select(\DB::raw("Impuesto_HC valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $equipo_medida = $db->table('Equipo_de_medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
        }

        $count_id = $contador2->id;

        $hoy = \Carbon\Carbon::now();

        $cont = $contador;
        $contador_label = $contador2->count_label;
        $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        \DB::disconnect('mysql2');

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
                else
                    $ctrl = 1;
                    if($tipo_count < 3)
                        return view('simulacion_facturas.simulacion_facturas',compact('user','titulo','id','precio_potencia','precio_energia','E_Activa', 'potencia_demandada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','total1','total2','total3','IVA','sumatoria','label_intervalo','ctrl','tipo_count','coste_potencia_contratada','contador_label','diasDiferencia','domicilio','dir_image_count','total_','coste_reactiva','tipo_tarifa','coste_potencia_contratada_max', 'count_id'));
                        else
                            return view('Gas.simulacion_facturas',compact('user','titulo','id','hoy','date_from','date_to','cont','label_intervalo','ctrl','tipo_count','contador_label','consumo_GN_kWh','consumo_GN_kWh_diario','I_E_HC','equipo_medida','precio_variable','precio_fijo','descuento','descuento_variable','coste_precio_fijo','coste_descuento_fijo','coste_termino_fijo','diasDiferencia','domicilio','dir_image_count','tipo_tarifa','coste_potencia_contratada_max', 'count_id'));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('simulacion_facturas.simulacion_facturas',compact('user','titulo','id','precio_potencia','precio_energia','E_Activa', 'potencia_demandada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','total1','total2','total3','IVA','sumatoria','label_intervalo','tipo_count','coste_potencia_contratada','contador_label','diasDiferencia','domicilio','dir_image_count','tipo_tarifa','coste_potencia_contratada_max'));
    }

    function SimulacionFacturaPdf($id,Request $request){
        // id representa el id del usuario que se desea ver  y $ctrl el control que indica que
        // la vista mostrada viene del panel administrativo

        $contador = strtolower(request()->input('contador'));
        $array_coste_activa = array();
        $coste_activa = 0;
        $array_coste_reactiva = array();
        $coste_reactiva = 0;
        $array_potencia_contratada = array();
        $potencia_contratada = 0;
        $array_exceso_potencia = array();
        $exceso_potencia = 0;
        $array_impuesto = array();
        $impuesto = 0;
        $array_equipo = array();
        $equipo = 0;
        // variables
        $E_Activa = array();
        $MES = array();
        $precio_energia = array();
        $E_Reactiva = array();
        $potencia_demandada = array();
        $coste_potencia_contratada_max = array();

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $interval = Session::get('_flash')['intervalos'];

        $titulo = 'Simulacion de Factura';

        $domicilio = '';

        $data_domicilio = $db->table("Area_Cliente")->select(\DB::raw("`SUMINISTRO DEL  DOMICILIO` as domicilio"))->first();
        if($data_domicilio)
        {
            $domicilio = $data_domicilio->domicilio;
        }

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                        }
                    }
                }else{
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Último Trimestre';
            break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 4 || $now == 5 || $now == 6){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 7 || $now == 8 || $now == 9){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 10 || $now == 11 || $now == 12){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Trimestre Actual';
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
            break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
            break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';
            break;
        }
        $fechaEmision = \Carbon\Carbon::parse($date_from);
        $fechaExpiracion = \Carbon\Carbon::parse($date_to);

        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);

        if($tipo_count < 3)
        {
            for ($i=1; $i < 7 ; $i++) {
                $periodos2[] = 'P'.$i;
            }

            $MES = $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();

            // COSTE DE LA ENERGÍA ACTIVA
            $precio_energia = $db->table('Precio_Energia')->select('precio')->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->orderBy('Periodo','ASC')->get()->toArray();
            if($tipo_tarifa == 1)
                $coste_energia = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            else
                $coste_energia = $db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_energia as $coste_ener) {
                    $aux_index = 'costeP';
                    $aux_coste_energia[$index][$aux_index.($index+1)] = $coste_ener->costeP1;
                    $aux_coste_energia[$index][$aux_index.($index+2)] = $coste_ener->costeP2;
                    $aux_coste_energia[$index][$aux_index.($index+3)] = $coste_ener->costeP3;
                    $aux_coste_energia[$index][$aux_index.($index+4)] = $coste_ener->costeP4;
                    $aux_coste_energia[$index][$aux_index.($index+5)] = $coste_ener->costeP5;
                    $aux_coste_energia[$index][$aux_index.($index+6)] = $coste_ener->costeP6;
                    $index++;
                }
            }else{
                foreach ($coste_energia as $coste_ener) {
                    $aux_index = 'costeP';
                    $aux_coste_energia[$index][$aux_index.($index+1)] = $coste_ener->costeP1;
                    $aux_coste_energia[$index][$aux_index.($index+2)] = $coste_ener->costeP2;
                    $aux_coste_energia[$index][$aux_index.($index+3)] = $coste_ener->costeP3;
                    $index++;
                }
            }


            $coste_energia = $aux_coste_energia;

            if($tipo_tarifa == 1)
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
            else
                $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());

            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                    $aux_coste_reactiva[$index][$aux_index.($index+4)] = $coste_reac->costeP4;
                    $aux_coste_reactiva[$index][$aux_index.($index+5)] = $coste_reac->costeP5;
                    $aux_coste_reactiva[$index][$aux_index.($index+6)] = $coste_reac->costeP6;
                    $index++;
                }
            }else{
                foreach ($db_coste_reactiva as $coste_reac) {
                    $aux_index = 'costeP';
                    $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                    $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                    $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                    $index++;
                }
            }

            $coste_reactiva = $aux_coste_reactiva;

            //CANTIDAD DE POTECIA CONSUMIDA EN KWH
            $potencia_demandada = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("Periodo, MAX(`Potencia Contratada (kW)`) potencia_demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

            // COSTE DE LA POTENCIA CONTRATADA
            $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo, Precio precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get()->toArray();
            if($tipo_tarifa == 1)
                $coste_potencia_contratada = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            else{
                // $coste_potencia_contratada = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                $coste_potencia_contratada = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("(MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P1' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P1' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P1' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P1', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P2' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P2' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P2' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P2', (MAX(CASE when (`Potencia_Contratada` * 0.85) > `Potencia_Maxima` AND `Periodo`='P3' then (`Potencia_Contratada` * 0.85) * `Precio` when (`Potencia_Contratada` * 0.85) <= `Potencia_Maxima` AND (`Potencia_Contratada` * 1.05) >= `Potencia_Maxima` AND `Periodo`='P3' then `Potencia_Maxima` * `Precio` when (`Potencia_Contratada` * 1.05) < `Potencia_Maxima` AND `Periodo`='P3' then ((((`Potencia_Maxima` - (`Potencia_Contratada` * 1.05)) * 2) + `Potencia_Maxima`) * `Precio`) END))*COUNT(DISTINCT`date`)*12/365 AS 'P3'"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $coste_potencia_contratada_max = $db->table('ZPI_Potencia_Maxima_Dia')->select(\DB::raw("MAX(Potencia_Maxima) maxima_po, RIGHT(Periodo,1) as periodo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

                $coste_max = array();
                for($i = 0; $i < count($coste_potencia_contratada); $i++)
                {
                    $max = new \stdClass();
                    $max->maxima_po = 0.0;
                    $max->periodo = $i + 1;
                    $coste_max[] = $max;
                }

                foreach($coste_potencia_contratada_max as $max_pot)
                {
                    $idx = intval($max_pot->periodo) - 1;
                    $coste_max[$idx] = $max_pot;
                }
                $coste_potencia_contratada_max = $coste_max;
            }

            $index = 0;
            if($tipo_tarifa == 1)
            {
                foreach ($coste_potencia_contratada as $coste_poten) {
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
                foreach ($coste_potencia_contratada as $coste_poten) {
                    $aux_index = 'costeP';
                    $aux_coste_potencia[$index][$aux_index.($index+1)] = $coste_poten->P1;
                    $aux_coste_potencia[$index][$aux_index.($index+2)] = $coste_poten->P2;
                    $aux_coste_potencia[$index][$aux_index.($index+3)] = $coste_poten->P3;
                    $index++;
                }
            }

            $coste_potencia_contratada = $aux_coste_potencia;
            $dias_potencia_contratada = $db->table('Coste_Potencia_Contratada')->select(\DB::raw("COUNT(*) dias"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get()->toArray();

            // EXCESOS DE POTENCIA
            if($contador2->database == 'Prueba_Contador_6.0_V3')
            {
                $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();
            }else{
                if($tipo_tarifa == 1)
                {
                    $db_excesos = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                    $index = 0;
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

                    $exceso_potencia = $aux_excesos;
                }
            }



            $j = 0;
            if(!empty($precio_potencia))
            {
                foreach ($precio_potencia as $pp) {
                    $precio_potencia[$j]->precio_potencia = ($pp->precio_potencia*12/365)*count($dias_potencia_contratada);
                    $j++;
                }
            }

            // foreach ($MES as $mes) {
            //     foreach ($periodos2 as $P)
            //     {
            $E_Activa = $db->table('Energia_Consumida_Activa_y_Reactiva')->select(\DB::raw("SUM(`Energia Activa (kWh)`) as Activa"))->where("date", '>=',$date_from)->where("date", '<=',$date_to)->groupBy('Periodo')->get()->toArray();
            // $E_Activa[] = $db->table('Consumo_Energia_Activa')->select(\DB::raw("SUM(`Energia Activa (kWh)`) as Activa"))->join('Tarifa',"Consumo_Energia_Activa.time",">=",\DB::raw("Tarifa.hora_start AND Tarifa.Mes = ".$mes->MES." AND Consumo_Energia_Activa.time < Tarifa.hora_end"))->where("Consumo_Energia_Activa.date", '>=',$date_from)->where("Consumo_Energia_Activa.date", '<=',$date_to)->where("Tarifa.Periodo",$P)->where(\DB::raw('MONTH(Consumo_Energia_Activa.date)'),$mes->MES)->get()->toArray();
            //     }
            // }
            $aux = array();
            $i = 0;
            $total1 = 0;
            $total_ = 0;
            $total2 = 0;
            $total3 = 0;

            if(!empty($E_Activa))
            {
                foreach ($E_Activa as $val) {
                    // PARTE DE TERMINO ENERGÍA ACTIVA
                    if(isset($coste_energia[0]))
                    {
                        $totales_parciales_energiaAct[] = $coste_energia[0]['costeP'.($i+1)];
                    }else{
                        $totales_parciales_energiaAct[]=0;
                    }
                    $total1 = $total1 + $totales_parciales_energiaAct[$i];

                    // PARTE DE ENERGÍA REACTIVA
                    if(isset($coste_reactiva[0]))
                    {
                        $totales_parciales_energiaReact[] = $coste_reactiva[0]['costeP'.($i+1)];
                    }else{
                        $totales_parciales_energiaReact[]=0;
                    }
                    $total_ = $total_ + $totales_parciales_energiaReact[$i];

                    // PARTE DE TÉRMINO DE POTENCIA
                    if($tipo_tarifa == 1)
                    {
                        if(isset($precio_potencia[$i%6]) && !empty($precio_potencia))
                        {
                            $totales_parciales_potencia[] = floatval($potencia_demandada[$i%6]->potencia_demandada)*floatval($precio_potencia[$i%6]->precio_potencia);
                        }else{
                            $totales_parciales_potencia[]=0;
                        }
                    }else{
                        $totales_parciales_potencia[]=0;
                    }

                    $total2 = $total2 + $totales_parciales_potencia[$i];;

                    $i++;
                }


            }else{
                $totales_parciales_energiaAct[] = 0;
                $total1= 0;
                $totales_parciales_potencia[]=0;
                $total2=0;
                $total3=0;
            }
            if($tipo_tarifa != 1){
                foreach ($coste_potencia_contratada[0] as $value) {
                    $totales_parciales_potencia[] = ($value);
                    $total2 = $total2 + $totales_parciales_potencia[$i];
                    $i++;
                }
            }

            if($tipo_tarifa == 1)
            {
                foreach ($exceso_potencia[0] as $exc) {
                    // TOTAL DE EXCESOS
                    $total3 = $total3 + ($exc);
                    $index++;
                }
            }

            if($contador2->iee == 3)
            {
                $aux_iee = 0;
            }elseif($contador2->iee == 2){
                $aux_iee = 0.15;
            }else{
                $aux_iee = 1;
            }

            $sumatoria = $total1 + $total2 + $total3 + $total_;
            $impuesto = $sumatoria*0.0511269632*$aux_iee;

            // if($tipo_tarifa == 1)
            // {
                $equipo = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
                if(!empty($equipo))
                {
                    foreach ($equipo as $value) {
                        $IVA = ($sumatoria + $impuesto + (floatval($value->valor)*($diasDiferencia+1)))*0.21;
                    }
                }else{
                    $IVA = ($sumatoria + $impuesto)*0.21;
                }
            // }else{
            //     $IVA = ($sumatoria + $impuesto)*0.21;
            // }
        }else{
            $consumo_GN_kWh = $db->table('Consumo_GN_kWh')->select(\DB::raw("SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

            $precio_variable = $db->table('Precio_variable')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $descuento_variable = $db->table('Descuento_variable')->select(\DB::raw("Descuento, Descuento_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $precio_fijo = $db->table('Precio')->select(\DB::raw("Precio, Precio_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $descuento = $db->table('Descuento')->select(\DB::raw("Descuento, Descuento_propuesto"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $coste_precio_fijo = $db->table('Coste_Precio_Fijo')->select(\DB::raw("SUM(`Coste Precio Fijo (€)`) Precio"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_descuento_fijo = $db->table('Coste_Descuento_Fijo')->select(\DB::raw("SUM(`Coste Descuento Fijo (€)`) Descuento"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $coste_termino_fijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) Precio"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

            $consumo_GN_kWh_diario = $db->table('ZPI_GN_kWh_diario')->select(\DB::raw("SUM(`Qd Diaria (kWh)`) consumo"))->where('date',$date_from)->get();

            $I_E_HC = $db->table('Impuesto_HC')->select(\DB::raw("Impuesto_HC valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();

            $equipo_medida = $db->table('Equipo_de_medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
        }

        if($tipo_count < 3)
        {
            $dataPlot = array();
            $dataPlot["Término de Energía"] = $total_ + $total1;
            if(isset($equipo) && is_array($equipo) && is_object($equipo[0]))
            {
                $dataPlot["Equipo de Medida"] = $equipo[0]->valor*($diasDiferencia+1);
            }
            else
            {
                $dataPlot["Equipo de Medida"] = 0;
            }
            $dataPlot["Término de Potencia"] = $total2 + $total3;
            $dataPlot["I.E.E."] = $impuesto;

            $dataPlot = serialize($dataPlot);
            $dataPlot = base64_encode($dataPlot);

            $pathImage = url("grafica_analisis_potencia/".$dataPlot);
            $file_name_plot = tempnam(sys_get_temp_dir(), "").".png";

            $process_command = "wkhtmltoimage --crop-x 0 --crop-w 600 --crop-y 0 --crop-h 600 ".$pathImage." ".$file_name_plot;
            $process = new Process($process_command);
            $process->run();

            if (!$process->isSuccessful()) {
                try
                {
                    $message_process = $process->getOutput();
                    $data = json_decode($message_process);
                    $msg_error = "Command: ". $process_command." \nResponse: ".$data;
                    Log::info($msg_error);
                }
                catch(Exception $error)
                {

                }

            }

            $resource_image = imagecreatefrompng ($file_name_plot);
            imagealphablending($resource_image,false);
            imagesavealpha($resource_image,true);

            $new_resource = imagecropauto($resource_image, IMG_CROP_WHITE);
            if($new_resource)
            {
                imagealphablending($new_resource,false);
                imagesavealpha($new_resource,true);
                imagepng ($new_resource, $file_name_plot , 6, PNG_ALL_FILTERS);
            }

        } else {

            $total1 = 0.0;
            if(isset($consumo_GN_kWh[0]) && isset($precio_variable))
            {
                $total1 += $consumo_GN_kWh[0]->consumo*$precio_variable->Precio;
            }
            if(isset($consumo_GN_kWh[0]) && isset($descuento_variable->Descuento))
            {
                $total1 += $consumo_GN_kWh[0]->consumo*(-1)*$descuento_variable->Descuento;
            }

            $total2 = 0.0;
            if(isset($coste_termino_fijo))
            {
                $total2 = $coste_termino_fijo->Precio;
            }

            $total3 = 0.0;
            if(isset($I_E_HC->valor) && isset($consumo_GN_kWh[0]->consumo))
            {
                $total3 = $consumo_GN_kWh[0]->consumo*$I_E_HC->valor;
            }

            $total4 = 0.0;
            if(isset($equipo_medida->valor))
            {
                $total4 = $equipo_medida->valor*($diasDiferencia+1);
            }

            $dataPlot = array();
            $dataPlot["Término Variable"] = floatval($total1);
            $dataPlot["Término Fijo"] = floatval($total2);
            $dataPlot["I.E.HC"] = floatval($total3);
            $dataPlot["Equipo de Medida"] = floatval($total4);

            $dataPlot = serialize($dataPlot);
            $dataPlot = base64_encode($dataPlot);

            $pathImage = url("grafica_analisis_potencia/".$dataPlot);
            $file_name_plot = tempnam(sys_get_temp_dir(), "").".png";

            $process_command = "wkhtmltoimage --crop-x 0 --crop-w 600 --crop-y 0 --crop-h 600 ".$pathImage." ".$file_name_plot;
            $process = new Process($process_command);
            $process->run();

            if (!$process->isSuccessful()) {
                try
                {
                    $message_process = $process->getOutput();
                    $data = json_decode($message_process);
                    $msg_error = "Command: ". $process_command." \nResponse: ".$data;
                    Log::info($msg_error);
                }
                catch(Exception $error)
                {

                }

            }

            $resource_image = imagecreatefrompng ($file_name_plot);
            imagealphablending($resource_image,false);
            imagesavealpha($resource_image,true);

            $new_resource = imagecropauto($resource_image, IMG_CROP_WHITE);
            if($new_resource)
            {
                imagealphablending($new_resource,false);
                imagesavealpha($new_resource,true);
                imagepng ($new_resource, $file_name_plot , 6, PNG_ALL_FILTERS);
            }
        }

        $hoy = \Carbon\Carbon::now();
        $cont = $contador;
        $contador_label = $contador2->count_label;
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$contador2->user_id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count, user_id) VALUES ('".$current_count."',".$contador2->user_id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$contador2->user_id);

        if(($contador2->user_id != 0 && Auth::user()->id == $contador2->user_id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$contador2->user_id)->get()->first();
            if(Auth::user()->tipo != 1)
            $ctrl = 0;
            else
                $ctrl = 1;

            $nombreArchivoPdf = $titulo."_".$contador_label."_".$date_from."_".$date_to.".pdf";
            if(!is_null($user->_perfil))
                $image = $user->_perfil->avatar;
            else{
                $image = "images/avatar.png";
            }

            if($tipo_count < 3)
                $pdf = \PDF::loadView('simulacion_facturas.simulacion_facturas_pdf',compact('user','titulo','id','precio_potencia','precio_energia','E_Activa', 'potencia_demandada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','total1','total2','total3','IVA','sumatoria','label_intervalo','ctrl','image','coste_potencia_contratada','contador_label','diasDiferencia','dir_image_count','coste_reactiva','total_','tipo_tarifa','coste_potencia_contratada_max', 'file_name_plot', 'domicilio'));
            else
                $pdf = \PDF::loadView('Gas.simulacion_facturas_pdf',compact('user','titulo','id','hoy','date_from','date_to','cont','label_intervalo','ctrl','tipo_count','contador_label','consumo_GN_kWh','consumo_GN_kWh_diario','I_E_HC','equipo_medida','precio_variable','precio_fijo','descuento','descuento_variable','image','ctrl','coste_precio_fijo','coste_descuento_fijo','coste_termino_fijo','diasDiferencia','dir_image_count','tipo_tarifa','coste_potencia_contratada_max', 'file_name_plot', 'domicilio'));
                $pdf->setPaper("Letter", "portrait");
            return $pdf->download($nombreArchivoPdf);
        }
        return \Redirect::to('https://submeter.es/');
    }

    function ContadoresPdf($id){
        $user = User::where('id',$id)->get()->first();
        $contador = strtolower(request()->input('contador'));
        $array_coste_activa = array();
        $coste_activa = array();
        $array_coste_reactiva = array();
        $coste_reactiva = array();
        $array_potencia_contratada = array();
        $potencia_contratada = array();
        $array_exceso_potencia = array();
        $exceso_potencia = array();
        $array_impuesto = array();
        $impuesto = array();
        $array_equipo = array();
        $equipo = array();
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

        $titulo = 'Resumen de Contadores';
        $hoy = \Carbon\Carbon::now();


        switch ($interval) {
            case '2':
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }

                $label_intervalo = 'Hoy';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }

                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                if($dont == 0)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }
                }elseif($now == 4 || $now == 7 || $now == 10){
                    // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    }
                }elseif($now == 5 || $now == 8 || $now == 11){
                    // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    }
                }elseif($now == 6 || $now == 9 || $now == 12){
                    // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Último Trimestre';
                break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
            break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
            break;

            default:
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
            break;
        }
        $i = 0;
        $fechaEmision = \Carbon\Carbon::parse($date_from);
        $fechaExpiracion = \Carbon\Carbon::parse($date_to);

        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
        $array_contadores = '';

        $uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
        $enterprise = Enterprise::find($uEnterprise->enterprise_id);
        $uMeters = UserEnergyMeters::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)->get();

        foreach ($uMeters as $uMeter)
        {
            $eMeter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)->where("meter_id", $uMeter->meter_id)->first();
            if(!$eMeter)
            {
                continue;
            }
            $contador2 = EnergyMeter::where("id", $uMeter->meter_id)->first();
            if(!$contador2)
            {
                continue;
            }
            config(['database.connections.mysql2.host' => $contador2->host]);
            config(['database.connections.mysql2.port' => $contador2->port]);
            config(['database.connections.mysql2.database' => $contador2->database]);
            config(['database.connections.mysql2.username' => $contador2->username]);
            config(['database.connections.mysql2.password' => $contador2->password]);
            env('MYSQL2_HOST',$contador2->host);
            env('MYSQL2_DATABASE',$contador2->port);
            env('MYSQL2_USERNAME', $contador2->username);
            env('MYSQL2_PASSWORD',$contador2->password);
            $tipo_tarifa = $contador2->tarifa;
            $tipo_count = $contador2->tipo;

            $db = \DB::connection('mysql2');

            if($tipo_count < 3)
            {
                $array_contadores = $array_contadores.'C'.($i+1).'= '.$contador2->count_label.', ';
                // $coste_activa[] = ($db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(`Coste Energia Activa (€)`) valor"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                if($tipo_tarifa == 1)
                    $db_coste_activa = ($db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                else{
                    $db_coste_activa = ($db->table('Coste_Energia_Activa')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                // dd($db_coste_activa);
                }

                $index = 0;
                if($tipo_tarifa == 1)
                {
                    foreach ($db_coste_activa as $coste_ac) {
                        $aux_index = 'costeP';
                        $aux_coste_activa[$index][$aux_index.($index+1)] = $coste_ac->costeP1;
                        $aux_coste_activa[$index][$aux_index.($index+2)] = $coste_ac->costeP2;
                        $aux_coste_activa[$index][$aux_index.($index+3)] = $coste_ac->costeP3;
                        $aux_coste_activa[$index][$aux_index.($index+4)] = $coste_ac->costeP4;
                        $aux_coste_activa[$index][$aux_index.($index+5)] = $coste_ac->costeP5;
                        $aux_coste_activa[$index][$aux_index.($index+6)] = $coste_ac->costeP6;
                        $index++;
                    }
                }else{
                    foreach ($db_coste_activa as $coste_ac) {
                        $aux_index = 'costeP';
                        $aux_coste_activa[$index][$aux_index.($index+1)] = $coste_ac->costeP1;
                        $aux_coste_activa[$index][$aux_index.($index+2)] = $coste_ac->costeP2;
                        $aux_coste_activa[$index][$aux_index.($index+3)] = $coste_ac->costeP3;
                        $aux_coste_activa[$index][$aux_index.($index+4)] = 0;
                        $aux_coste_activa[$index][$aux_index.($index+5)] = 0;
                        $aux_coste_activa[$index][$aux_index.($index+6)] = 0;
                        $index++;
                    }
                }

                $coste_activa[] = $aux_coste_activa;

                // $coste_activa = floatval(\DB::select("SELECT SUM(`Coste Energia Activa (€)`) valor FROM ".$contador.".coste_energia_activa WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                if($tipo_tarifa == 1)
                    $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                else
                    $db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;

                if($tipo_tarifa == 1)
                {
                    foreach ($db_coste_reactiva as $coste_reac) {
                        $aux_index = 'costeP';
                        $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                        $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                        $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                        $aux_coste_reactiva[$index][$aux_index.($index+4)] = $coste_reac->costeP4;
                        $aux_coste_reactiva[$index][$aux_index.($index+5)] = $coste_reac->costeP5;
                        $aux_coste_reactiva[$index][$aux_index.($index+6)] = $coste_reac->costeP6;
                        $index++;
                    }
                }else{
                    foreach ($db_coste_reactiva as $coste_reac) {
                        $aux_index = 'costeP';
                        $aux_coste_reactiva[$index][$aux_index.($index+1)] = $coste_reac->costeP1;
                        $aux_coste_reactiva[$index][$aux_index.($index+2)] = $coste_reac->costeP2;
                        $aux_coste_reactiva[$index][$aux_index.($index+3)] = $coste_reac->costeP3;
                        $aux_coste_reactiva[$index][$aux_index.($index+4)] = 0;
                        $aux_coste_reactiva[$index][$aux_index.($index+5)] = 0;
                        $aux_coste_reactiva[$index][$aux_index.($index+6)] = 0;
                        $index++;
                    }
                }

                $coste_reactiva[] = $aux_coste_reactiva;

                // $coste_reactiva = floatval(\DB::select("SELECT SUM(`Coste Energia Reactiva (€)`) valor FROM ".$contador.".coste_energia_reactiva WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                if($tipo_tarifa == 1)
                    $db_potencia_contratada = ($db->table('Coste_Potencia_Contratada')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                else
                    $db_potencia_contratada = $db->table('Coste_Termino_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3"))->where('Max(`date`)','<=',$date_to)->get()->toArray();

                $index = 0;
                if($tipo_tarifa == 1)
                {
                    foreach ($db_potencia_contratada as $poten_contra) {
                        $aux_index = 'costeP';
                        $aux_potencia_contratada[$index][$aux_index.($index+1)] = $poten_contra->costeP1;
                        $aux_potencia_contratada[$index][$aux_index.($index+2)] = $poten_contra->costeP2;
                        $aux_potencia_contratada[$index][$aux_index.($index+3)] = $poten_contra->costeP3;
                        $aux_potencia_contratada[$index][$aux_index.($index+4)] = $poten_contra->costeP4;
                        $aux_potencia_contratada[$index][$aux_index.($index+5)] = $poten_contra->costeP5;
                        $aux_potencia_contratada[$index][$aux_index.($index+6)] = $poten_contra->costeP6;
                        $index++;
                    }
                }else{
                    foreach ($db_potencia_contratada as $poten_contra) {
                        $aux_index = 'costeP';
                        $aux_potencia_contratada[$index][$aux_index.($index+1)] = $poten_contra->costeP1;
                        $aux_potencia_contratada[$index][$aux_index.($index+2)] = $poten_contra->costeP2;
                        $aux_potencia_contratada[$index][$aux_index.($index+3)] = $poten_contra->costeP3;
                        $aux_potencia_contratada[$index][$aux_index.($index+4)] = 0;
                        $aux_potencia_contratada[$index][$aux_index.($index+5)] = 0;
                        $aux_potencia_contratada[$index][$aux_index.($index+6)] = 0;
                        $index++;
                    }
                }
                $potencia_contratada[] = $aux_potencia_contratada;

                // $potencia_contratada = floatval(\DB::select("SELECT SUM(`Coste Potencia Contratada (€)`) valor FROM ".$contador.".coste_potencia_contratada WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                // dd($cont['database']);
                if($contador2->database == 'Prueba_Contador_6.0_V3')
                {
                    $db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();
                }else{
                    if($tipo_tarifa == 1)
                        $db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
                }
                    // $db_exceso_potencia = ($db->table('Coste_Exceso_Potencia')->select(\DB::raw("SUM(P1) costeP1, SUM(P2) costeP2, SUM(P3) costeP3, SUM(P4) costeP4, SUM(P5) costeP5, SUM(P6) costeP6"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray());
                $index = 0;
                if($tipo_tarifa == 1)
                {
                    foreach ($db_exceso_potencia as $exceso_poten) {
                        $aux_index = 'costeP';
                        $aux_exceso_potencia[$index][$aux_index.($index+1)] = $exceso_poten->costeP1;
                        $aux_exceso_potencia[$index][$aux_index.($index+2)] = $exceso_poten->costeP2;
                        $aux_exceso_potencia[$index][$aux_index.($index+3)] = $exceso_poten->costeP3;
                        $aux_exceso_potencia[$index][$aux_index.($index+4)] = $exceso_poten->costeP4;
                        $aux_exceso_potencia[$index][$aux_index.($index+5)] = $exceso_poten->costeP5;
                        $aux_exceso_potencia[$index][$aux_index.($index+6)] = $exceso_poten->costeP6;
                        $index++;
                    }
                }else{
                    $aux_index = 'costeP';
                    $aux_exceso_potencia[$index][$aux_index.($index+1)] = 0;
                    $aux_exceso_potencia[$index][$aux_index.($index+2)] = 0;
                    $aux_exceso_potencia[$index][$aux_index.($index+3)] = 0;
                    $aux_exceso_potencia[$index][$aux_index.($index+4)] = 0;
                    $aux_exceso_potencia[$index][$aux_index.($index+5)] = 0;
                    $aux_exceso_potencia[$index][$aux_index.($index+6)] = 0;
                    $index++;
                }

                $exceso_potencia[] = $aux_exceso_potencia;
                // dd($aux_exceso_potencia);

                // $exceso_potencia = floatval(\DB::select("SELECT SUM(`Coste Exceso Potencia (€)`) valor FROM ".$contador.".coste_exceso_potencia WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);
                $aux_impuesto = 0;
                $index = 0;

                if($contador2->iee == 3)
                {
                    $aux_iee = 0;
                }elseif($contador2->iee == 2){
                    $aux_iee = 0.15;
                }else{
                    $aux_iee = 1;
                }

                foreach ($aux_coste_activa[0] as $coste) {
                    $aux_index = 'costeP';
                    $aux_impuesto = $aux_impuesto+(($coste + $coste_reactiva[$i][0][$aux_index.($index+1)]*0 + $potencia_contratada[$i][0][$aux_index.($index+1)] + $exceso_potencia[$i][0][$aux_index.($index+1)])*0.0511269632)*$aux_iee;
                    $index++;
                }
                $impuesto[] = $aux_impuesto;
                if($tipo_count == 1)
                {
                    $iee[] = $aux_impuesto;
                }

                $equipo[] = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
            }else{
                $array_contadores = $array_contadores.'G'.($i+1).'= '.$contador2->count_label.', ';

                $termino_variable[] = $db->table('Coste_Termino_Variable')->select(\DB::raw("SUM(`Coste Termino Variable (€)`) valor"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $termino_fijo[] = $db->table('Coste_Termino_Fijo')->select(\DB::raw("SUM(`Coste Termino Fijo (€)`) valor"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $equipo_medida[] = $db->table('Equipo_de_medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

                $consumo_GN_kWh[] = $db->table('Consumo_GN_kWh')->select(\DB::raw("SUM(`Consumo GN (kWh)`) consumo"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();

                $I_E_HC[] = $db->table('Impuesto_HC')->select(\DB::raw("Impuesto_HC valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->first();
            }
            // dd($equipo);
            $i++;
            \DB::disconnect('mysql2');
        }
        $nombreContador = "Totalizador";
        $cont = $contador;
        // dd($coste_activa);
        // dd($impuesto);
        $nombreArchivoPdf = $titulo."_".$nombreContador."_".$date_from."_".$date_to.".pdf";
        if(!is_null($user->_perfil))
        {
            $image = $user->_perfil->avatar;
        }else{
            $image = "images/avatar.png";
        }
        if($tipo_count < 3)
            $pdf = \PDF::loadView('Dashboard.dashboard_pdf',compact('user','titulo','id','coste_activa', 'coste_reactiva', 'potencia_contratada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','label_intervalo','image','array_contadores','diasDiferencia','iee'));
        else
            $pdf = \PDF::loadView('Gas.contadores_pdf',compact('user','titulo','id','hoy','date_from','date_to','cont','label_intervalo','image','array_contadores','diasDiferencia','termino_variable','termino_fijo','equipo_medida','consumo_GN_kWh','I_E_HC','iee'));

        $pdf->setPaper("A4", "portrait");
        return $pdf->download($nombreArchivoPdf);
    }

    function InformesPeriodicosAlertas($id,Request $request)
    {
        $contador = (request()->input('contador'));
        $user = User::find($id);
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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');
        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }elseif($now == 4 || $now == 7 || $now == 10){
                    $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                }elseif($now == 5 || $now == 8 || $now == 11){
                    $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                }elseif($now == 6 || $now == 9 || $now == 12){
                    $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                }
                $label_intervalo = 'Ultimo Trimestre';
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $label_intervalo = 'Último Año';
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Hoy';
                break;
        }

        $user = Auth::user();//usuario logeado
        $titulo = 'Informes Periódicos';//Título del content
        $cont = $current_count;
        $informes_programados1 = Informes::where('user_id',$id)->where('contador',$cont)->get()->toArray();
        $informes_analizadores_programados1 = Informes_analizadores::where('user_id',$id)->where('contador',$cont)->get()->toArray();
        $alertas = Alertas::where('user_id',$id)->where('contador',$cont)->get()->toArray();

        $informes_programados = array();
        $informes_analizadores_programados = array();
        $alertas_programadas = array();


        for ($i=0; $i <5 ; $i++)
        {
            $flag = 0;
            foreach ($informes_programados1 as $key) {
                if($key['check']-1 == $i)
                {
                    $informes_programados[] = $key;
                    $flag = 1;
                }
            }
            if($flag == 0)
                $informes_programados[] = NULL;
        }

        for ($i=0; $i <5 ; $i++)
        {
            $flag = 0;
            foreach ($informes_analizadores_programados1 as $key) {
                if($key['check']-1 == $i)
                {
                    $informes_analizadores_programados[] = $key;
                    $flag = 1;
                }
            }
            if($flag == 0)
                $informes_analizadores_programados[] = NULL;
        }

        for ($i=1; $i <3 ; $i++)
        {
            $flag = 0;
            foreach ($alertas as $key) {
                if($key['alert_type'] == $i)
                {
                    $alertas_programadas[] = $key;
                    $flag = 1;
                }
            }
            if($flag == 0)
                $alertas_programadas[] = NULL;
        }
        // dd($alertas_programadas);
        $contador_label = $contador2->count_label;
        // dd($contador_label);

        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);
        if(is_null($aux_current_count) || empty($aux_current_count))
        {
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        }
        else
        {
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);
        }

        $porcentajes_alerta = [];
        $search_types = [];
        if($tipo_count == 1)
        {
            $porcentajes_alerta = [4=>'5%', 1 => '10%', 2 => '15%', 3=>'20%'];
        }
        elseif($tipo_count == 2)
        {
            $porcentajes_alerta = [4=>'0', 5=>'5%', 6=>'10%', 7=>'20%', 8=>'25%', 9=>'30%', 10=>'40%', 1 => '50%', 2 => '70%',
                                    3=>'90%'];
            $search_types = [1 => '≥', 2 => '≤', 3 => '='];
        }
        elseif($tipo_count == 3)
        {
            $porcentajes_alerta = [1 => '10%', 2 => '15%', 3=>'20%'];
        }

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
            {
                $ctrl = 0;
            }
            else
            {
                $ctrl = 1;
            }
            return view('informes_periodicos_alertas.informes_periodicos_alertas',compact('user','titulo','cliente','id',
                    'ctrl','label_intervalo','date_from', 'date_to','cont','informes_programados', 'informes_analizadores_programados', 'alertas_programadas',
                    'tipo_count','contador_label','dir_image_count','tipo_tarifa', 'porcentajes_alerta', 'search_types'));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('informes_periodicos_alertas.informes_periodicos_alertas',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from', 'date_to','cont','informes_programados','alertas_programadas','tipo_count','contador_label','dir_image_count','tipo_tarifa'));
    }

    function InformesProgramados($id,Request $request)
    {
        $contador = (request()->input('contador'));
        $user = User::find($id);
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

        for ($i=1; $i < 6; $i++)
        {
            $infor_verify = Informes::where('user_id', $id)->where('check',$i)->where('meter_id',$contador2->id)->first();

            if(isset($request["my_checkbox".$i]) && $request["destinatarios".$i] !== NULL)
            {
                if(!is_null($infor_verify))
                {
                    $infor_verify->emails = $request["destinatarios".$i];
                    $infor_verify->meter_id = $contador2->id;
                    $infor_verify->save();
                }else{
                    $infor_verify = new Informes();
                    $infor_verify->check = $i;
                    $infor_verify->emails = $request["destinatarios".$i];
                    $infor_verify->contador = $contador2->count_label;
                    $infor_verify->meter_id = $contador2->id;
                    $infor_verify->user_id = $id;
                    $infor_verify->save();
                }
            }else{
                if(!is_null($infor_verify))
                {
                    if($infor_verify->check == $i)
                    {
                        $infor_verify->delete();
                    }
                }
            }
        }
        Session::flash('message', 'Se han programado con éxito los informes');

        return \Redirect::back();
    }

    function AlertasProgramadas($id, Request $request)
    {
        $contador = (request()->input('contador'));
        $user = User::find($id);
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

        for ($i=6; $i < 8; $i++)
        {
            $infor_verify = Alertas::where('user_id',$id)->where('alert_type',$i-5)->where('meter_id', $contador2->id)->first();

            if(!is_null($request["destinatarios".$i]))
            {
                $search_type = $request->get("select".($i-5)."_search_type", 1);
                if(!is_null($infor_verify))
                {
                    $infor_verify->alert_value = $request["select".($i-5)];
                    $infor_verify->search_type = $search_type;
                    $infor_verify->emails = $request["destinatarios".$i];
                    $infor_verify->meter_id = $contador2->id;
                    $infor_verify->save();
                }else{
                    $infor = new Alertas();
                    $infor->alert_type = $i-5;
                    $infor->alert_value = $request["select".($i-5)];
                    $infor->search_type = $search_type;
                    $infor->emails = $request["destinatarios".$i];
                    $infor->contador = $contador2->count_label;
                    $infor->meter_id = $contador2->id;
                    $infor->user_id = $id;
                    $infor->save();
                }
            }else{
                if(!is_null($infor_verify))
                {
                    if($infor_verify->alert_type == $i-5)
                    {
                        $infor_verify->delete();
                    }
                }
            }
        }
        Session::flash('message', 'Se han programado con éxito las alertas');
        return \Redirect::back();
    }

    function AnalizadoresInformesProgramados($id,Request $request)
    {
        $contador = (request()->input('contador'));
        $user = User::find($id);
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



        for ($i=8; $i < 13; $i++)
        {
            $infor_verify = informes_analizadores::where('user_id', $id)->where('check',$i-7)->where('meter_id',$contador2->id)->first();

            if(isset($request["my_checkbox".$i]) && $request["destinatarios".$i] !== NULL)
            {
                if(!is_null($infor_verify))
                {
                    $infor_verify->emails = $request["destinatarios".$i];
                    $infor_verify->meter_id = $contador2->id;
                    $infor_verify->save();
                }else{
                    $infor = new Informes_analizadores();
                    $infor->check = $i - 7;
                    $infor->emails = $request["destinatarios".$i];
                    $infor->contador = $contador2->count_label;
                    $infor->meter_id = $contador2->id;
                    $infor->user_id = $id;
                    $infor->save();
                }
            }
            else{
                if(!is_null($infor_verify))
                {
                    if($infor_verify->check == $i-7)
                    {
                        $infor_verify->delete();
                    }
                }
            }
        }



        Session::flash('message', 'Se han programado con éxito los informes de analizadores');
        return \Redirect::back();
    }




    function AnalizadoresInformesAlertasProgramados($id,$user_id,Request $request)
    {
            if(isset($request["my_checkbox1"])){
              $informes = 1;
            }else{
              $informes = 0;
            }

            if(isset($request["my_checkbox2"])){
              $alertas = 1;
            }else{
              $alertas = 0;
            }

            $infor_verify = analyzer_alertas_informes::where('analyzer_id', $id)->where('user_id', $user_id)->first();

                if(!is_null($infor_verify))
                {
                  if($informes == 0 && $alertas == 0){
                    $infor_verify->delete();
                  }else{
                    $infor_verify->informes = $informes;
                    $infor_verify->alertas = $alertas;
                    $infor_verify->user_id = $user_id;
                    $infor_verify->save();
                  }
                }elseif($informes == 0 && $alertas == 0){

                }else{
                  $infor = new analyzer_alertas_informes();
                  $infor->analyzer_id = $id;
                  $infor->informes = $informes;
                  $infor->alertas = $alertas;
                  $infor->user_id = $user_id;
                  $infor->save();
                }

        Session::flash('message', 'Se han programado con éxito los informes de analizadores');
        return \Redirect::back();
    }

    function ExportarDatos($id,Request $request)
    {
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 4 || $now == 7 || $now == 10){
                    // $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                    }
                }elseif($now == 5 || $now == 8 || $now == 11){
                    // $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                    }

                }elseif($now == 6 || $now == 9 || $now == 12){
                    // $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    // $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                        $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';
                break;
            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 4 || $now == 5 || $now == 6){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 7 || $now == 8 || $now == 9){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                }elseif($now == 10 || $now == 11 || $now == 12){
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                }
                $label_intervalo = 'Trimestre Actual';
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
            break;
            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Último Año';
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
            break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Hoy';
            break;
        }

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        if($tipo_count < 3)
        {
            $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();

            $datos_contador = $db->table('Datos_Contador')->select(\DB::raw("date, time, `EAct imp(kWh)` EAct_imp, `EAct exp(kWh)` EAct_exp, `ERInd imp(kvarh)` EReac_imp, `ERInd exp(kvarh)` EReac_ind, `ERCap exp(kvarh)` EReac_cap_exp, `ERCap imp(kvarh)` EReac_Camp_imp"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            $aux_cont_datos = count($datos_contador);
            if($aux_cont_datos == 0)
            {
                $aux_cont_datos = 1;
            }

            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            $total4 = 0;
            $total5 = 0;
            $total6 = 0;
            foreach ($datos_contador as $value) {
                $total1 += $value->EAct_imp;
                $value->EAct_imp = number_format($value->EAct_imp,0,',','.');
                $total2 += $value->EAct_exp;
                $value->EAct_exp = number_format($value->EAct_exp,0,',','.');
                $total3 += $value->EReac_imp;
                $value->EReac_imp = number_format($value->EReac_imp,0,',','.');
                $total4 += $value->EReac_ind;
                $value->EReac_ind = number_format($value->EReac_ind,0,',','.');
                $total5 += $value->EReac_cap_exp;
                $value->EReac_cap_exp = number_format($value->EReac_cap_exp,0,',','.');
                $total6 += $value->EReac_Camp_imp;
                $value->EReac_Camp_imp = number_format($value->EReac_Camp_imp,0,',','.');
            }
        }else{

            $datos_contador = $db->table('Datos_Contador')->select(\DB::raw("date, time, `Volumen_bruto (m3)` volumen_bruto, `Volumen_neto (m3)` volumen_neto, `Caudal_neto (m3/s)` caudal_neto,`Caudal_bruto (m3/s)` caudal_bruto, `Factor_correccion` factor_correccion, `Presion (bar)` presion"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get()->toArray();
            $aux_cont_datos = count($datos_contador);
            if($aux_cont_datos == 0)
            {
                $aux_cont_datos = 1;
            }
            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            $total4 = 0;
            $total5 = 0;
            $total6 = 0;
            foreach ($datos_contador as $value) {
                $total1 += $value->volumen_bruto;
                $value->volumen_bruto = number_format($value->volumen_bruto,0,',','.');
                $total2 += $value->volumen_neto;
                $value->volumen_neto = number_format($value->volumen_neto,0,',','.');
                $total3 += $value->caudal_neto;
                $value->caudal_neto = number_format($value->caudal_neto,0,',','.');
                $total4 += $value->caudal_bruto;
                $value->caudal_bruto = number_format($value->caudal_bruto,0,',','.');
                $total5 += $value->factor_correccion;
                $value->factor_correccion = number_format($value->factor_correccion,0,',','.');
                $total6 += $value->presion;
                $value->presion = number_format($value->presion,0,',','.');
            }
        }
        // dd($datos_contador);
        /*$contador_label = $contador2->count_label;
         \DB::disconnect('mysql2');

         $user = Auth::user();//usuario logeado
         $titulo = 'Exportar Datos';//Título del content
         $cont = $contador;
         if($id != 0)
         {
         $user = User::where('id',$id)->get()->first();
         if(Auth::user()->tipo != 1)
         $ctrlcaudal_bruto;
         $value->caudal_bruto = number_format($value->caudal_bruto,0,',','.');
         $total5 += $value->factor_correccion;
         $value->factor_correccion = number_format($value->factor_correccion,0,',','.');
         $total6 += $value->presion;
         $value->presion = number_format($value->presion,0,',','.');
         }
         }  */
         //dd($datos_contador);
         $contador_label = $contador2->count_label;
         if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
         \DB::disconnect('mysql2');

         $user = Auth::user();//usuario logeado
         $titulo = 'Exportar Datos';//Título del content
         $cont = $contador;

         $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);


         if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
         {
             $user = User::where('id',$id)->get()->first();
             if(Auth::user()->tipo != 1)
                 $ctrl = 0;
                 else
                     $ctrl = 1;

                     return view('exportar_datos.exportar_datos',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from', 'date_to','cont','tipo_count','datos_contador','contador_label','total1','total2','total3','total4','total5','total6','domicilio','dir_image_count','aux_cont_datos','tipo_tarifa'));
         }
        return \Redirect::to('https://submeter.es/');
         // return view('exportar_datos.exportar_datos',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from', 'date_to','cont','tipo_count','datos_contador','contador_label','total1','total2','total3','total4','total5','total6','domicilio','dir_image_count','aux_cont_datos','tipo_tarifa'));
    }

    function GetExportar(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');
        if($tipo_count < 3)
        {
            $datos_contador = $db->table('Datos_Contador')->select(\DB::raw("date, time, `EAct imp(kWh)` EAct_imp, `EAct exp(kWh)` EAct_exp, `ERInd imp(kvarh)` EReac_imp, `ERInd exp(kvarh)` EReac_ind, `ERCap exp(kvarh)` EReac_cap_exp, `ERCap imp(kvarh)` EReac_Camp_imp"))->where('date','>=',$request->date_from)->where('date','<=',$request->date_to)->orderBy("date", "asc")->orderBy("time", "asc")->get()->toArray();
        }
        else
        {
            $datos_contador = $db->table('Datos_Contador')->select(\DB::raw("date, time, `Volumen_bruto (m3)` volumen_bruto, `Volumen_neto (m3)` volumen_neto, `Caudal_neto (m3/s)` caudal_neto,`Caudal_bruto (m3/s)` caudal_bruto, `Factor_correccion` factor_correccion, `Presion (bar)` presion"))->where('date','>=',$request->date_from)->where('date','<=',$request->date_to)->orderBy("date", "asc")->orderBy("time", "asc")->get()->toArray();
        }

                // \DB::select("SELECT date, time, `EAct imp(kWh)` EAct_imp, `EAct exp(kWh)` EAct_exp, `ERInd imp(kvarh)` EReac_imp, `ERInd exp(kvarh)` EReac_ind, `ERCap exp(kvarh)` EReac_cap_exp, `ERCap imp(kvarh)` EReac_Camp_imp FROM ".$request->cont.".`datos_contador` WHERE date >= '".$request->date_from."' AND date <= '".$request->date_to."'");
                if(!is_null($user->_perfil))
                    $image = $user->_perfil->avatar;
                    else{
                        $image = "images/avatar.png";
                    }
                    if(1){

                        $filename = "Datos_".$request->cont."_".$request->date_from."-".$request->date_to.".csv";
                        $handle = fopen($filename, 'w+');
                        if($tipo_count < 3)
                        {
                            fputcsv($handle, array('Fecha', 'Tiempo', 'EAct imp(kWh)', 'EAct exp(kWh)','ERInd imp(kvarh)','ERInd exp(kvarh)','ERCap exp(kvarh)','ERCap imp(kvarh)'),';');
                            foreach($datos_contador as $data) {
                                fputcsv($handle, array($data->date, $data->time, $data->EAct_imp, $data->EAct_exp, $data->EReac_imp, $data->EReac_ind, $data->EReac_cap_exp, $data->EReac_Camp_imp),';');
                            }
                        }else{

                            fputcsv($handle, array('Fecha', 'Tiempo', 'Volumen Bruto (m3)', 'Volumen Neto (m3)','Caudal Neto (m3/s)','Caudal Bruto (m3/s)','Factor Correción','Presion (bar)'),';');
                            foreach($datos_contador as $data) {
                                fputcsv($handle, array($data->date, $data->time, $data->volumen_bruto, $data->volumen_neto, $data->caudal_neto, $data->caudal_bruto, $data->factor_correccion, $data->presion),';');
                            }
                        }


                        fclose($handle);

                        $headers = array(
                                        'Content-Type' => 'text/csv',
                        );
                        return Response::download($filename, $filename, $headers);
                    }
                    \DB::disconnect('mysql2');
    }

    function edit($id)
    {
        $user = Auth::user();
        $client = User::with('_count')->find($id);
        $count_id = array();
        $analizadores = array();
        $j = 0;

        foreach ($client->_count as $value)
        {
            $count_id[] = $value->id;

            config(['database.connections.mysql2.host' => $value->host]);
            config(['database.connections.mysql2.port' => $value->port]);
            config(['database.connections.mysql2.database' => $value->database]);
            config(['database.connections.mysql2.username' => $value->username]);
            config(['database.connections.mysql2.password' => $value->password]);
            env('MYSQL2_HOST',$value->host);
            env('MYSQL2_DATABASE',$value->database);
            env('MYSQL2_USERNAME', $value->username);
            env('MYSQL2_PASSWORD', $value->password);

            $value->tipo_contrato = 0;
            $value->tipo_usuario = 0;
            $value->url_image = "";
            $db = null;
            $data_cliente = null;
            try {
                \DB::connection('mysql2')->getPdo();
                $db = \DB::connection('mysql2');
                $data_cliente = $db->table("Area_Cliente")->first();
            }
            catch (\Exception $e) {

            }

            if($db !== null && $data_cliente !== null)
            {
                $value->tipo_contrato = $data_cliente->TIPO_DE_CONTRATO_ENERGIA;
                $value->tipo_usuario = $data_cliente->PERFIL_USUARIO;
                if(file_exists(public_path($data_cliente->LOGOTIPO)))
                {
                    $value->url_image = asset($data_cliente->LOGOTIPO);
                }
            }

            if(!empty(Analizador::where('count_id',$value->id)->get()->toArray()))
                $analizadores[] = Analizador::where('count_id',$value->id)->get();
        }

        $grupos = Groups::orderBy("nombre", "asc")->get();

        $contadores = count($client->_count);
        $tipo_count = 0;
        if(Auth::user()->tipo == 1)
            return view('User.edit', compact('user', 'contadores', 'client','tipo_count','analizadores', 'grupos'));
        return \Redirect::to('https://submeter.es/');
    }

    function update(Request $request, $id)
    {
        $user = User::with('_count')->find($id);
        $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => 'required|email',
                        // 'contadores' => 'required|numeric|digits_between:1,10|min:1',
        ],[
                        'numeric' => 'El campo Contadores debe ser numérico',
                        'email' => 'El campo Correo debe ser de tipo email',
                        'required' => 'El campo es requerido',
                        // 'contadores.min' => 'El campo Contadores debe ser mínimo :min',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }
        // $i =0;
        // dd($request['name_cont_'.$i]);
        if($user->tipo == 1){
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
        }else{
            $nombre = $request->name;

            $user->name = $nombre;
            $user->email = $request->email;
            $user->save();

            $cont = $user->_count;
            $bandera = 0;

            $j=1;
            for ($i=0; $i < count($cont); $i++) {

                $contador = Count::where('id', $cont[$i]->id)->first();

                Informes::where('contador',$contador->count_label)->update(['contador' => $request['name_cont_'.$i]]);
                Alertas::where('contador',$contador->count_label)->update(['contador' => $request['name_cont_'.$i]]);
                if(!empty($contador))
                {
                    $contador->count_label = $request['name_cont_'.$i];
                    $contador->user_id = $user->id;
                    $contador->host = $request['val_host_'.$i];
                    $contador->database = $request['val_dbase_'.$i];
                    $contador->port = $request['val_port_'.$i];
                    $contador->username = $request['val_username_'.$i];
                    $contador->password = $request['val_password_'.$i];
                    $contador->tipo = $request['tipo_'.$i];
                    $contador->tarifa = $request['tarifa_'.$i];
                    $contador->group_id = $request['groups'][$i];
                    $contador->save();

                    config(['database.connections.mysql2.host' => $contador->host]);
                    config(['database.connections.mysql2.port' => $contador->port]);
                    config(['database.connections.mysql2.database' => $contador->database]);
                    config(['database.connections.mysql2.username' => $contador->username]);
                    config(['database.connections.mysql2.password' => $contador->password]);
                    env('MYSQL2_HOST',$contador->host);
                    env('MYSQL2_DATABASE',$contador->database);
                    env('MYSQL2_USERNAME', $contador->username);
                    env('MYSQL2_PASSWORD', $contador->password);

                    $db = null;
                    try {
                        \DB::purge('mysql2');
                        \DB::connection('mysql2')->getPdo();
                        $db = \DB::connection('mysql2');
                    }
                    catch (\Exception $e) {

                    }

                    if($db !== null)
                    {
                        $data_area = $db->table("Area_Cliente")->first();
                        $db->table("Area_Cliente")->where("ID", $data_area->ID)
                                    ->update(["TIPO_DE_CONTRATO_ENERGIA" => \DB::raw(intval($request['tipo_contrato_'.$i])),
                                        "PERFIL_USUARIO" => \DB::raw(intval($request['tipo_usuario_'.$i])) ]);
                        $file_logo = $request->file("avatar_".$i);
                        if($file_logo)
                        {
                            $this->updateLogoContador($db, $file_logo, $contador->ID);
                        }
                    }

                    if(isset($contador->_analizador[0]))
                    {

                        $cont_analizador = count($contador->_analizador);
                        Analizador::where('count_id',$contador->_analizador[0]->count_id)->delete();
                        for ($k=0; $k < $cont_analizador; $k++)
                        {
                            $analizador = new Analizador();
                            $analizador->label = $request['name_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->host = $request['val_host_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->database = $request['val_dbase_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->port = $request['val_port_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->username = $request['val_username_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->password = $request['val_password_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->count_id = $cont[$i]->id;
                            if($k == 0 )
                                $analizador->principal = 1;
                                else
                                    $analizador->principal = 0;
                                    $analizador->color_etiqueta = $request['val_color_analizador'.$k.'_contador_'.($i+1)];
                                    $analizador->save();
                                    $bandera = 1;
                        }
                        // $cont_analizador = count($contador->_analizador);
                        for ($k=0; $k < $request['analizadores_'.$i]; $k++)
                        {
                            $analizador = new Analizador();
                            $analizador->label = $request['name_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->host = $request['val_host_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->database = $request['val_dbase_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->port = $request['val_port_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->username = $request['val_username_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->password = $request['val_password_analizador'.($k+$cont_analizador).'_contador_'.($i+1)];
                            $analizador->count_id = $cont[$i]->id;
                            if($k == 0 && $bandera == 0)
                                $analizador->principal = 1;
                                else
                                    $analizador->principal = 0;
                                    $analizador->color_etiqueta = $request['val_color_analizador'.$k.'_contador_'.($i+1)];
                                    $analizador->save();
                        }
                    }else{
                        for ($k=0; $k < $request['analizadores_'.$i]; $k++)
                        {
                            $analizador = new Analizador();
                            $analizador->label = $request['name_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->host = $request['val_host_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->database = $request['val_dbase_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->port = $request['val_port_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->username = $request['val_username_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->password = $request['val_password_analizador'.($k).'_contador_'.($i+1)];
                            $analizador->count_id = $cont[$i]->id;
                            if($k == 0 && $bandera == 0)
                                $analizador->principal = 1;
                                else
                                    $analizador->principal = 0;
                                    $analizador->color_etiqueta = $request['val_color_analizador'.$k.'_contador_'.($i+1)];
                                    $analizador->save();
                        }
                    }
                }else
                {

                }

                $j++;
            }

            if($request['cantidad_new_cont'] > 0)
            {
                for ($i=count($cont); $i < ($request['cantidad_new_cont']); $i++)
                {
                    $contador_new = new Count();
                    $contador_new->count_label = $request['name_cont_'.$i];
                    $contador_new->user_id = $user->id;
                    $contador_new->host = $request['val_host_'.$i];
                    $contador_new->database = $request['val_dbase_'.$i];
                    $contador_new->port = $request['val_port_'.$i];
                    $contador_new->username = $request['val_username_'.$i];
                    $contador_new->password = $request['val_password_'.$i];
                    $contador_new->tipo = $request['tipo_'.$i];
                    $contador_new->tarifa = $request['tarifa_'.$i];
                    $contador_new->save();

                    config(['database.connections.mysql2.host' => $value->host]);
                    config(['database.connections.mysql2.port' => $value->port]);
                    config(['database.connections.mysql2.database' => $value->database]);
                    config(['database.connections.mysql2.username' => $value->username]);
                    config(['database.connections.mysql2.password' => $value->password]);
                    env('MYSQL2_HOST',$value->host);
                    env('MYSQL2_DATABASE',$value->database);
                    env('MYSQL2_USERNAME', $value->username);
                    env('MYSQL2_PASSWORD', $value->password);

                    $db = null;
                    try {
                        \DB::connection('mysql2')->getPdo();
                        $db = \DB::connection('mysql2');
                        $data_cliente = $db->table("Area_Cliente")->first();
                    }
                    catch (\Exception $e) {

                    }

                    if($db !== null)
                    {
                        $db->table("Area_Cliente")->update(["TIPO_DE_CONTRATO_ENERGIA" => $request['tipo_contrato_'.$i],
                            "PERFIL_USUARIO" => $request['tipo_usuario_'.$i] ]);
                        $file_logo = $request->get("avatar_".$i);
                        $this->updateLogoContador($db, $file_logo, $contador->id);
                    }

                    for ($k=0; $k < $request['analizadores_'.$i]; $k++)
                    {
                        $analizador = new Analizador();
                        $analizador->label = $request['name_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->host = $request['val_host_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->database = $request['val_dbase_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->port = $request['val_port_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->username = $request['val_username_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->password = $request['val_password_analizador'.$k.'_contador_'.($i+1)];
                        $analizador->count_id = $contador_new->id;
                        if($k == 0)
                        {
                            $analizador->principal = 1;
                        }
                        else
                        {
                            $analizador->principal = 0;
                            $analizador->color_etiqueta = $request['val_color_analizador'.$k.'_contador_'.($i+1)];
                            $analizador->save();
                        }
                    }
                }
            }

        }

        Session::flash('message', 'El usuario ' . $user->name . ' ha sido editado con éxito!.');
        if(Auth::user()->tipo == 1)
            return redirect()->to('administrar_usuarios/'. $user->tipo.'/0');
        return \Redirect::to('https://submeter.es/');
    }

    function EliminarContador(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            $count = Count::find($data['contador_id']);
            if($count)
            {
                $count->delete();
            }
            // $id_product = \DB::select("select id from products where referencia = '".$data."'");
            //\DB::delete('DELETE FROM counts WHERE id = ? ',array($data['contador_id']));
            // return ['data' => $request];
        }
    }

    function EliminarAnalizador(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            // $id_product = \DB::select("select id from products where referencia = '".$data."'");
            \DB::delete('DELETE FROM analizadors WHERE id = ? ',array($data['analizador_id']));
            return ['data' => $request];
        }
    }

    function perfilForm()
    {
        $user = Auth::user();

        $tipo_count = strtolower(request()->input('tipo'));
        if(empty($tipo_count))
        {
            $tipo_count = Count::where('user_id',3)->first()->tipo;

        }

        $usuario = User::with('_perfil')->find($user->id);
        $perfil = $usuario->_perfil;

        if(is_null($perfil)){
            $perfil = new Perfil;
        }
        // if(Auth::user()->tipo == 1)
            return view('User.perfil', compact('user', 'perfil','tipo_count'));
        return \Redirect::to('https://submeter.es/');
    }

    function storePerfil(PerfilUserRequest $request,$id)
    {
        $usuario = User::with('_perfil')->find($id);
        $perfil = $usuario->_perfil;

        if ($perfil) {

            if (!is_null($request->avatar)) {
                $perfil->avatar = $this->setImage($request);
            }

            $perfil->update($request->only(['direccion', 'fijo', 'movil', 'user_id', 'denominacion_social', 'domicilio_social', 'domicilio_suministro', 'cups', 'cif', 'empresa_distribuidora', 'empresa_comercializadora', 'persona_contacto', 'tarifa']));
            Session::flash('message', 'El perfil fue actualizado con éxito');

        }else{

            $perfil = new Perfil($request->all());
            $perfil->avatar = $this->setImage($request);
            $perfil->save();

            Session::flash('message', 'Se almaceno con éxito los datos de perfil');
        }

        if ($request->password) {
            $validator = Validator::make($request->only(['password', 'repeat']), [
                            'password' => 'min:6',
                            'repeat' => 'required',
            ],[
                            'required' => 'El campo es obligatorio',
                            // 'confirmed' => 'Las contraseñas no son iguales',
                            'min' => 'La contraseña debe tener mínimo :min caracteres',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }

            Session::flash('message', 'La contraseña fue editada con éxito');

            $usuario->password = Hash::make($request->password);
            $usuario->save();
        }

        return \Redirect::back();
    }

    private function setImage($request)
    {

        if ($request->hasFile('avatar'))
        {
            $file = Input::file('avatar');
            $destinationHead = 'images/avatares';
            $folderAvatar = File::makeDirectory($destinationHead, $mode = 0777, true, true);
            $name = $file->getClientOriginalName();
            $file->move($destinationHead, $name);

            $route_avatar = $destinationHead.'/'.$name;
        }else{
            $route_avatar = 'images/avatar.png';
        }

        return $route_avatar;
    }

    function areaCliente($id, Request $request){

        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $tarifa = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        if($tipo_count < 3)
        {
            $potencia_contratada = $db->table('Potencia_Contratada')->get();
            $precio_energia = $db->table('Precio_Energia')->orderBy('Periodo','ASC')->get();
            $precio_potencia = $db->table('Precio_Potencia')->orderBy('Periodo','ASC')->get();
            // if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Alquiler_Equipo_Medida'")->first())
            // {
                $alquiler_equipo_medida = $db->table('Alquiler_Equipo_Medida')->get();
            // if(Auth::user()->id == 18)
            //     dd($alquiler_equipo_medida, $contador2);
            // }
            $factor = $db->table('Coeficiente_Emisiones')->select(\DB::raw("`Coeficiente Emisiones` coeficiente"))->where('Coeficiente Emisiones','>=',0)->get();
            // dd($alquiler_equipo_medida);
            $precio_p = 0;
            $precio_e = 0;

            foreach ($precio_potencia as $p_pot) {
                $precio_p+= $p_pot->Precio;
            }

            foreach ($precio_energia as $p_ene) {
                $precio_e+= $p_ene->precio;
            }

            $impuesto = ($precio_p + $precio_e)*0.511;
            $reduccion = $impuesto*0.85;
        }else{
            $QD_contratado = $db->table('Caudal_diario_contratado')->first();
            $precio_variable = $db->table('Precio_variable')->first();
            $descuento_variable = $db->table('Descuento_variable')->first();
            $precio_fijo = $db->table('Precio')->first();
            $descuento_fijo = $db->table('Descuento')->first();
            $PCS = $db->table('Poder_calorifico_superior')->first();
            $I_E_HC = $db->table('Impuesto_HC')->first();
            $equipo_medida = $db->table('Equipo_de_medida')->first();
            $factor_CO2 = $db->table('Coeficiente_de_emisiones')->select(\DB::raw("`Coeficiente Emisiones` coeficiente"))->first();
            // $tarifa = $db->table('Area_Cliente')->select(\DB::raw("`TARIFA` tarifa"))->first();
        }

        $data_base_contador = $contador2->database;

        $contador_label = $contador2->count_label;
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();

        $iee_cont = $contador2->iee;



        $aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

        if(is_null($aux_current_count) || empty($aux_current_count))
            \DB::insert("INSERT INTO current_count (label_current_count,user_id) VALUES ('".$current_count."',".$id.")");
        else
            \DB::update("UPDATE current_count SET label_current_count = '".$current_count."' WHERE user_id = ".$id);


        $data_linea_base = [];
        $data_representacion = [];
        $data_keys_linea_base = [];
        $data_linea_base["values_linea"] = [];
        $data_linea_base["fecha_inicio_linea_base"] = '0000-00-00';
        $data_linea_base["fecha_fin_linea_base"] = '0000-00-00';
        $data_representacion["fecha_inicio_representacion"] = '0000-00-00';

        $data_cliente = new \stdClass();
        $data_cliente->tipo_contrato = 0;
        $data_cliente->tipo_usuario = 0;

        $namesDays = ["1"=>"Dom", "2"=>"Lun", "3"=>"Mar", "4"=>"Mie", "5"=>"Jue", "6"=>"Vie", "7"=>"Sab"];

        $lineas_base = $db->table('Linea_Base')->select(\DB::raw('`EAct imp(kWh)` as potencia_linea, DiaDeSemana as dia, date_start, date_end'))->get();
        $fecha_inicio_linea_base = '0000-00-00';
        $fecha_fin_linea_base = '0000-00-00';
        foreach($lineas_base as $linea)
        {
            $dia = $linea->dia;
            $dia = ($dia + 7) % 9;
            $linea->name = $namesDays[$linea->dia];
            $data_keys_linea_base[$dia] = $linea;
            $fecha_inicio_linea_base = $linea->date_start;
            $fecha_fin_linea_base = $linea->date_end;
        }
        ksort($data_keys_linea_base);
        $data_linea_base["values_linea"] = array_values($data_keys_linea_base);
        $data_linea_base["fecha_inicio_linea_base"] = $fecha_inicio_linea_base;
        $data_linea_base["fecha_fin_linea_base"] = $fecha_fin_linea_base;

        if($tipo_count < 3)
        {
            $costes_representacion = $db->table('Costes_De_Representacion')->select(\DB::raw('date_start as fecha_inicio, date_end as fecha_fin, precio, RIGHT(Periodo,1) as periodo'))->orderBY("Periodo")->get();

            $arregloCoste = [];
            $min_potencia = 10000;
            $max_potencia = 0;
            foreach($costes_representacion as $representacion)
            {
                $data_representacion["fecha_inicio_representacion"] = $representacion->fecha_inicio;
                $data_representacion["fecha_fin_representacion"] = $representacion->fecha_fin;
                $arregloCoste[$representacion->periodo - 1] = $representacion->precio;
                if($min_potencia > $representacion->periodo - 1)
                {
                    $min_potencia = $representacion->periodo - 1;
                }
                if($max_potencia < $representacion->periodo - 1)
                {
                    $max_potencia = $representacion->periodo - 1;
                }
            }

            for($i = $min_potencia; $i <= $max_potencia; $i++)
            {
                if(!array_key_exists($i, $arregloCoste))
                {
                    $arregloCoste[$i] = 0.0;
                }
            }
            $arregloCoste = array_values($arregloCoste);
            $data_representacion["costes"] = $arregloCoste;
        }
        else
        {
            $costes_representacion = $db->table('Costes_De_Representacion')->select(\DB::raw('date_start as fecha_inicio, date_end as fecha_fin, precio'))->first();

            $arregloCoste = [];
            $data_representacion["fecha_inicio_representacion"] = $costes_representacion->fecha_inicio;
            $data_representacion["fecha_fin_representacion"] = $costes_representacion->fecha_fin;
            $arregloCoste[0] = $costes_representacion->precio;
            $data_representacion["costes"] = $arregloCoste;
        }

        $data_cliente = $db->table('Area_Cliente')->select(\DB::raw('`TIPO_DE_CONTRATO_ENERGIA` as tipo_contrato, `PERFIL_USUARIO` as tipo_usuario'))->first();




        \DB::disconnect('mysql2');

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
            {
                $ctrl = 0;
            }
            else
            {
                $ctrl = 1;
            }
            if($tipo_count < 3)
            {
                return view('User.area_cliente', compact('user', 'potencia_contratada', 'precio_energia', 'precio_potencia', 'alquiler_equipo_medida', 'impuesto', 'reduccion', 'factor','ctrl','tipo_count','contador_label','tarifa','dir_image_count','tipo_tarifa'
                            ,'iee_cont', 'data_linea_base', 'data_cliente', 'data_representacion'));
            }
            else
            {
                return view('Gas.area_cliente', compact('user', 'ctrl','tipo_count','contador_label','QD_contratado','precio_variable','descuento_variable','precio_fijo','descuento_fijo','PCS','I_E_HC','equipo_medida','factor_CO2','tarifa','dir_image_count','tipo_tarifa','iee_cont',
                    'data_linea_base', 'data_cliente', 'data_representacion'));
            }
        }
        return \Redirect::to('https://submeter.es/');
        // return view('User.area_cliente', compact('user', 'potencia_contratada', 'precio_energia', 'precio_potencia', 'alquiler_equipo_medida', 'impuesto', 'reduccion', 'factor','tipo_count','contador_label','dir_image_count','tipo_tarifa'));
    }

    function storeAreaCliente(Request $request, $id){
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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
        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');
        if($tipo_count < 3)
        {
            if($tipo_tarifa == 1)
            {
                for ($i=1; $i <= 6 ; $i++)
                {
                    $potencia = intval(str_replace(".", "", $request['contratada'.$i]));

                    $db->table('Potencia_Contratada')->where('Periodo',$request['perido'.strval($i)])->update(['date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Potencia_contratada' => $potencia, 'Periodo' => $request['perido'.strval($i)]]);

                    $db->table('Precio_Energia')->where('Periodo',$request['perido'.strval($i)])->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'precio' => str_replace(',', '.', ($request['energia'.strval($i)])), 'Periodo' => $request['perido'.strval($i)]));

                    $db->table('Precio_Potencia')->where('Periodo',$request['perido'.strval($i)])->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Precio' => str_replace(',', '.', $request['potencia'.strval($i)]), 'Periodo' => $request['perido'.strval($i)]));
                }
                if(isset($request['reduccion']))
                {
                    if($request['reduccion'] == '85')
                    {
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 2;
                            $val_cont->save();
                        }
                    }elseif($request['reduccion'] == '100'){
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 3;
                            $val_cont->save();
                        }
                    }else{
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 1;
                            $val_cont->save();
                        }
                    }
                }

            }else{
                for ($i=1; $i <= 3 ; $i++)
                {
                    $potencia = intval(str_replace(".", "", $request['contratada'.$i]));

                    $db->table('Potencia_Contratada')->where('Periodo',$request['perido'.strval($i)])->update(['date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Potencia_contratada' => $potencia, 'Periodo' => $request['perido'.strval($i)]]);

                    $db->table('Precio_Energia')->where('Periodo',$request['perido'.strval($i)])->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'precio' => str_replace(',', '.', ($request['energia'.strval($i)])), 'Periodo' => $request['perido'.strval($i)]));

                    $db->table('Precio_Potencia')->where('Periodo',$request['perido'.strval($i)])->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Precio' => str_replace(',', '.', $request['potencia'.strval($i)]), 'Periodo' => $request['perido'.strval($i)]));
                }
                if(isset($request['reduccion']))
                {
                    if($request['reduccion'] == '85')
                    {
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 2;
                            $val_cont->save();
                        }
                    }elseif($request['reduccion'] == '100'){
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 3;
                            $val_cont->save();
                        }
                    }else{
                        $contadors = EnergyMeter::where('database',$contador2->database)->get();
                        foreach ($contadors as $val_cont)
                        {
                            $val_cont->iee = 1;
                            $val_cont->save();
                        }
                    }
                }
            }

            if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Alquiler_Equipo_Medida' AND column_name = 'Alquiler_Equipo_Medida'")->first())
            {
                $db->table('Alquiler_Equipo_Medida')->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Alquiler_Equipo_Medida' => str_replace(',', '.', $request->equipo_medida)));
            }
            // \DB::insert('insert into '.$contador.'.alquiler_equipo_medida (date_start, date_end, Alquiler_Equipo_Medida) values (?,?,?)', array($request->date_start, '2050-01-01', $request->equipo_medida));

            $db->table('Coeficiente_Emisiones')->update(array('date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Coeficiente Emisiones' => str_replace(',', '.', $request->factor)));
            if(!is_null($db->table('Area_Cliente')))
            {
                $tarifa = $db->table('Area_Cliente')->select(\DB::raw("COUNT(*) cont"))->first();

                if($tarifa->cont == 1)
                    $db->table('Area_Cliente')->update(array('DENOMINACIÓN SOCIAL' => $request->denominacion_social, 'SOCIAL DOMICILIO' => $request->social_domicilio, 'SUMINISTRO DEL  DOMICILIO' => $request->suministro_del_domicilio, 'CUPS' => $request->cups, 'CIF' => $request->cif, 'DISTRIBUIDORA EMPRESA' => $request->empresa_distribuidora, 'COMERCIALIZADORA EMPRESA' => $request->empresa_comercializadora, 'CONTACTO DE  PERSONA' => $request->contacto_persona,'TELÉFONO' => $request->telefono,'CONTACTO O  AYUDA' => $request->contacto_ayuda,'TARIFA' => $request->tarifa));
                else
                    $db->table('Area_Cliente')->insert(array('DENOMINACIÓN SOCIAL' => $request->denominacion_social, 'SOCIAL DOMICILIO' => $request->social_domicilio, 'SUMINISTRO DEL  DOMICILIO' => $request->suministro_del_domicilio, 'CUPS' => $request->cups, 'CIF' => $request->cif, 'DISTRIBUIDORA EMPRESA' => $request->empresa_distribuidora, 'COMERCIALIZADORA EMPRESA' => $request->empresa_comercializadora, 'CONTACTO DE  PERSONA' => $request->contacto_persona,'TELÉFONO' => $request->telefono,'CONTACTO O  AYUDA' => $request->contacto_ayuda,'TARIFA' => $request->tarifa));
            }

            // $db->table('Potencia_Contratada')->update(['date_start' => $request->date_start, 'date_end' => '2050-01-01', 'Potencia_contratada' => $request['contratada'.$i], 'Periodo' => $request['perido'.$i]])->where('Periodo',$request['periodo'.$i]);
            // \DB::insert('insert into '.$contador.'.coeficiente_emisiones (date_start, date_end, `Coeficiente Emisiones`) values (?,?,?)', array($request->date_start, '2050-01-01', $request->factor));
        }else{

            $db->table('Caudal_diario_contratado')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Caudal_diario_contratado' => str_replace(',', '.', $request['QD_contratado'])]);

            $db->table('Precio_variable')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Precio' => str_replace(',', '.', $request['precio_variable'])]);

            $db->table('Descuento_variable')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Descuento' => str_replace(',', '.', $request['descuento_variable'])]);

            $db->table('Precio')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Precio' => str_replace(',', '.', $request['precio_fijo'])]);

            $db->table('Descuento')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Descuento' => str_replace(',', '.', $request['descuento_fijo'])]);

            $db->table('Poder_calorifico_superior')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'PCS' => str_replace(',', '.', $request['PCS'])]);

            $db->table('Impuesto_HC')->update(['date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Impuesto_HC' => str_replace(',', '.', $request->impuesto)]);

            $db->table('Equipo_de_medida')->update(array('date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Alquiler_Equipo_Medida' => str_replace(',', '.', $request->equipo_medida)));

            $db->table('Coeficiente_de_emisiones')->update(array('date_start' => $request->date_start, 'date_end' => '2099-01-01', 'Coeficiente Emisiones' => str_replace(',', '.', $request->factor)));
            $tarifa = $db->table('Area_Cliente')->select(\DB::raw("COUNT(*) cont"))->first();

            if(isset($request['reduccion']))
            {
                if($request['reduccion'] == '85')
                {
                    $contadors = Count::where('database',$contador2->database)->get();
                    foreach ($contadors as $val_cont)
                    {
                        $val_cont->iee = 2;
                        $val_cont->save();
                    }
                }elseif($request['reduccion'] == '100'){
                    $contadors = Count::where('database',$contador2->database)->get();
                    foreach ($contadors as $val_cont)
                    {
                        $val_cont->iee = 3;
                        $val_cont->save();
                    }
                }
            }

            if($tarifa->cont == 1)
                $db->table('Area_Cliente')->update(array('DENOMINACIÓN SOCIAL' => $request->denominacion_social, 'SOCIAL DOMICILIO' => $request->social_domicilio, 'SUMINISTRO DEL  DOMICILIO' => $request->suministro_del_domicilio, 'CUPS' => $request->cups, 'CIF' => $request->cif, 'DISTRIBUIDORA EMPRESA' => $request->empresa_distribuidora, 'COMERCIALIZADORA EMPRESA' => $request->empresa_comercializadora, 'CONTACTO DE  PERSONA' => $request->contacto_persona,'TELÉFONO' => $request->telefono,'CONTACTO O  AYUDA' => $request->contacto_ayuda,'TARIFA' => $request->tarifa));
            else
                $db->table('Area_Cliente')->insert(array('DENOMINACIÓN SOCIAL' => $request->denominacion_social, 'SOCIAL DOMICILIO' => $request->social_domicilio, 'SUMINISTRO DEL  DOMICILIO' => $request->suministro_del_domicilio, 'CUPS' => $request->cups, 'CIF' => $request->cif, 'DISTRIBUIDORA EMPRESA' => $request->empresa_distribuidora, 'COMERCIALIZADORA EMPRESA' => $request->empresa_comercializadora, 'CONTACTO DE  PERSONA' => $request->contacto_persona,'TELÉFONO' => $request->telefono,'CONTACTO O  AYUDA' => $request->contacto_ayuda,'TARIFA' => $request->tarifa));
        }

        $file_logo = $request->file("file_logo");
        $this->updateLogoContador($db, $file_logo, $id);

        $dias_linea = $request->get("dias_linea");
        $potencias_linea = $request->get("potencias_linea");
        //Agregamos Fecha Inicio - Fecha Fin línea base
        $fecha_inicio_linea_base = $request->get("fecha_inicio_linea_base");
        $fecha_fin_linea_base = $request->get("fecha_fin_linea_base");

        if(count($dias_linea) > 0 && count($potencias_linea) > 0)
        {
            foreach($dias_linea as $index => $dia)
            {
                $potencias_linea[$index] = str_replace(".", "", $potencias_linea[$index]);
                $db->table('Linea_Base')->where('DiaDeSemana', $dia)->update(['EAct imp(kWh)' => $potencias_linea[$index], 
                    'date_start'=>$fecha_inicio_linea_base, "date_end"=>$fecha_fin_linea_base
                ]);
            }
        }

        $fecha_inicio_representacion = $request->get("fecha_inicio_representacion");
        $fecha_fin_representacion = $request->get("fecha_fin_representacion");
        $costes_representacion = $request->get("costes_representacion");
        if(count($costes_representacion) > 0)
        {
            if($tipo_count < 3)
            {
                foreach($costes_representacion as $index => $coste)
                {
                    $coste = str_replace(",", ".", $coste);
                    $db->table('Costes_De_Representacion')->where("Periodo", "=", "P".($index + 1))
                    ->update(['precio' => $coste, 'date_start'=>$fecha_inicio_representacion, "date_end"=>$fecha_fin_representacion]);
                }
            }
            else
            {
                foreach($costes_representacion as $index => $coste)
                {
                    $coste = str_replace(",", ".", $coste);
                    $db->table('Costes_De_Representacion')
                       ->update(['precio' => $coste, 'date_start'=>$fecha_inicio_representacion, "date_end"=>$fecha_fin_representacion]);
                }
            }
        }

        \DB::disconnect('mysql2');

        Session::flash('message', 'Los datos para el contador '.($contador). ' fueron almacenados con éxito!');
        return \Redirect::back()->with(compact('tipo_tarifa'));

    }

    private function updateLogoContador($db, $file_logo, $id)
    {
        $max_size = 360;
        if($file_logo && $file_logo->isValid())
        {
            $mime_type = $file_logo->getMimeType();
            $tmp_path = $file_logo->getRealPath();
            $size = getimagesize($tmp_path);
            $file_resource = null;
            $image_width = $size[0];
            $image_height = $size[1];
            if($mime_type == "image/png")
            {
                $file_resource = imagecreatefrompng($tmp_path);
            }
            else if($mime_type == "image/jpeg")
            {
                $file_resource = imagecreatefromjpeg($tmp_path);
            }
            if($file_resource !== null)
            {
                if($image_width > $max_size || $image_height > $max_size)
                {
                    $scale = 0.0;
                    if($image_height > $image_width)
                    {
                        $scale = $max_size / $image_height;
                    }
                    else
                    {
                        $scale = $max_size / $image_width;
                    }
                    $new_width = round($image_width * $scale, 0);
                    $new_height = round($image_height * $scale, 0);
                    $resource_dst = imagecreatetruecolor($new_width, $new_height);
                    imagealphablending($resource_dst, false);
                    imagesavealpha($resource_dst, true);
                    imagecopyresampled($resource_dst, $file_resource, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
                    imagepng($resource_dst, $tmp_path, 6, PNG_ALL_FILTERS);
                }

                $new_name = $id.$file_logo->getClientOriginalName().microtime();
                $new_name = hash('ripemd160', $new_name).".png";
                $file_destination = public_path("images/avatares/");
                $file_logo->move($file_destination, $new_name);

                $imagen = $db->table('Area_Cliente')->select(\DB::raw('LOGOTIPO as imagen'))->first();
                $image_route = public_path($imagen->imagen);
                if(file_exists($image_route) && !is_dir($image_route))
                {
                    unlink($image_route);
                }
                $db->table('Area_Cliente')->update(['LOGOTIPO' => "images/avatares/".$new_name]);
            }
        }
    }

    function deleteConditions(Request $request){

        if($request->ajax()){
            $data = $request->all();
            $contador = $data['contador'];

            // if(empty($contador))
            // {
            //     $contador = strtolower(Count::where('user_id',$data['user_id'])->first()->count_label);
            // }
            if(empty($contador))
            {
                $contador = strtolower(Count::where('user_id',$data['user_id'])->first());

            }else{
                $contador2 = Count::where('count_label',$contador)->first();

            }
            config(['database.connections.mysql2.host' => $contador2->host]);
            config(['database.connections.mysql2.port' => $contador2->port]);
            config(['database.connections.mysql2.database' => $contador2->database]);
            config(['database.connections.mysql2.username' => $contador2->username]);
            config(['database.connections.mysql2.password' => $contador2->password]);
            env('MYSQL2_HOST',$contador2->host);
            env('MYSQL2_DATABASE',$contador2->database);
            env('MYSQL2_USERNAME', $contador2->username);
            env('MYSQL2_PASSWORD',$contador2->password);
            try {
                \DB::connection('mysql2')->getPdo();
            } catch (\Exception $e) {
                Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
                return \Redirect::back();
            }
            $db = \DB::connection('mysql2');

            $db->table('Potencia_Contratada')->truncate();
            //$db->table('Precio_Energia')->truncate();
            //$db->table('Precio_Potencia')->truncate();
            //$db->table('Alquiler_Equipo_Medida')->truncate();
            //$db->table('Coeficiente_Emisiones')->truncate();

            // \DB::table($contador.'.precio_energia')->truncate();
            // \DB::table($contador.'.precio_potencia')->truncate();
            // \DB::table($contador.'.alquiler_equipo_medida')->truncate();
            // \DB::table($contador.'.coeficiente_emisiones')->truncate();
            \DB::disconnect('mysql2');
            return ['data' => 'success'];
        }
    }

    function Analizadores($id,Request $request)
    {
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

        $interval = "";
        $flash_current_count = null;
        $session = Session::get('_flash');
        if(array_key_exists('intervalos', $session))
        {
            $interval = $session['intervalos'];
        }

        $dataRequest = [];
        $dataRequest["user"] = $user;
        $dataRequest["contador"] = $contador;
        $dataRequest["interval"] = $interval;
        $dataRequest["flash_current_count"] = $flash_current_count;

        $contador2 = ContadorController::getCurrrentController($dataRequest);


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

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
        $total_energias = array();

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`ESQUEMA ELECTRICO` esquema'))->first()))
            $esquema_electrico = $db->table('Area_Cliente')->select(\DB::raw('`ESQUEMA ELECTRICO` esquema'))->first()->esquema;
        else
            $db->table('Area_Cliente')->select(\DB::raw('`ESQUEMA ELECTRICO` esquema'))->first();

        if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Area_Cliente' AND column_name = '`LOGOTIPO`'")->first())
        {
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        }

        \DB::disconnect('mysql2');
        $analizadores = [];
        foreach ($contador2->analyzer_meters as $aMeter)
        {
            if(!$aMeter->meter)
            {
                continue;
            }
            if(!$this->checkAnalizadorUser($aMeter->analyzer_id, $user->id))
            {
                continue;
            }
            $meter = $aMeter->meter;
            if($aMeter->analyzer)
            {
                $analizador = $aMeter->analyzer;
                $analizadores[] = $aMeter->analyzer;
                config(['database.connections.mysql2.host' => $analizador->host]);
                config(['database.connections.mysql2.port' => $analizador->port]);
                config(['database.connections.mysql2.database' => $analizador->database]);
                config(['database.connections.mysql2.username' => $analizador->username]);
                config(['database.connections.mysql2.password' => $analizador->password]);
                env('MYSQL2_HOST',$analizador->host);
                env('MYSQL2_DATABASE',$analizador->database);
                env('MYSQL2_USERNAME', $analizador->username);
                env('MYSQL2_PASSWORD',$analizador->password);
                try {
                    \DB::connection('mysql2')->getPdo();
                } catch (\Exception $e) {
                    Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
                    return \Redirect::back();
                }
                $db = \DB::connection('mysql2');

                try
                {
                    $total_energias[] = $db->table('Analizadores_Tipo')->select(\DB::raw("SUM(`ENEact (kWh)`) energia_activa, SUM(`ENErea (kVArh)`) energia_reactiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();
                }
                catch(\Exception $e)
                {
                    $energia = new \stdClass();
                    $energia->energia_activa = 0;
                    $energia->energia_reactiva = 0;
                    $total_energias[] = $energia;
                }


            }
            \DB::disconnect('mysql2');
        }

        $contador_label = $contador2->count_label;

        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
            {
                $ctrl = 0;
            }
            else
            {
                $ctrl = 1;
            }
            return view('analizadores.analizadores',compact('user','titulo','id','label_intervalo','ctrl','contador_label','tipo_count','total_energias','date_from','date_to','analizadores','domicilio','dir_image_count','esquema_electrico','tipo_tarifa'));
        }
        return \Redirect::to('https://submeter.es/');
    }

    public static function checkAnalizadorUser($analyzer_id, $user_id)
    {
        $available = false;
        $uEnterprise = EnterpriseUser::where("user_id", $user_id)->first();
        if($uEnterprise)
        {
            $uAnalyzer = UserAnalyzers::where("user_id", $user_id)
                            ->where("analyzer_id", $analyzer_id)->where("enterprise_id", $uEnterprise->enterprise_id)->first();
            if($uAnalyzer)
            {
                $available = true;
            }
        }
        return $available;
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

    function AnalizadoresGraficas($user_id, $group_id, $id, Request $request)
    {
        $informes_alertas_analizadores = analyzer_alertas_informes::where('analyzer_id', $id)->where('user_id', $user_id)->first();
        if(is_null($informes_alertas_analizadores)) {
          $informes_programados = null;
          $alertas_programados = null;
        }else{
          $informes_programados = $informes_alertas_analizadores->informes;
          $alertas_programados = $informes_alertas_analizadores->alertas;
        }

        $analizador = Analizador::where('id',$id)->first();
        $color_etiqueta = $analizador->color_etiqueta;
        $count_id = $analizador->count_id;
        $user = User::where('id',$user_id)->first();

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
        $dataRequest["contador"] = "";
        $dataRequest["interval"] = $interval;
        $dataRequest["flash_current_count"] = $flash_current_count;
        $contador2 = ContadorController::getCurrrentController($dataRequest);
        $tipo_tarifa = $contador2->tarifa;

        $aux_contador = $contador2;
        $eje = array();
        $values_graficas_ = array();
        $values_corriente_ = array();
        $td_Frecuencia=array();
        $td_COSPHI=array();
        $td_FDP=array();
        $td_Intensidad=array();
        $td_PotReac=array();
        $td_PotAct_avg=array();
        $td_PotAct_max=array();
        $td_aux=array();
        $td_PotAct_total=array();
        $td_PotReac_total=array();



        config(['database.connections.mysql2.host' => $analizador->host]);
        config(['database.connections.mysql2.port' => $analizador->port]);
        config(['database.connections.mysql2.database' => $analizador->database]);
        config(['database.connections.mysql2.username' => $analizador->username]);
        config(['database.connections.mysql2.password' => $analizador->password]);
        env('MYSQL2_HOST',$analizador->host);
        env('MYSQL2_DATABASE',$analizador->database);
        env('MYSQL2_USERNAME', $analizador->username);
        env('MYSQL2_PASSWORD',$analizador->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        $interval = Session::get('_flash')['intervalos'];
        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ayer")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Ayer';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, SUM(`POWact1 (kW)`) potencia_activa_L1, SUM(`POWact2 (kW)`) potencia_activa_L2, SUM(`POWact3 (kW)`) potencia_activa_L3"))->where('date',$date_from)->groupBy('time')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, SUM(`IAC1 (A)`) corriente_L1, SUM(`IAC2 (A)`) corriente_L2, SUM(`IAC3 (A)`) corriente_L3"))->where('date',$date_from)->groupBy('time')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') time, `FRE (Hz)` FRE, `COSPHI` COSPHI, `PF1` PF1, `PF2` PF2, `PF3` PF3, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2, `IAC3 (A)` IAC3, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWact1 (kW)` POWact1_avg, `POWact2 (kW)` POWact2_avg, `POWact3 (kW)` POWact3_avg, `POWact1 (kW)` POWact1_max, `POWact2 (kW)` POWact2_max, `POWact3 (kW)` POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

                foreach ( $table_data as $i=>$dat ) {
                    $td_aux[$i]['time'] = "$dat->time";
                    $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                    $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                    $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                    $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                    $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                    $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                    $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                    $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                    $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                    $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                    $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                    $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                    $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                    $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');

                }

                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == 'Semana Actual')
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Actual';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje,DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, `date` date, DATE_FORMAT(time, '%H:%i') time, `FRE (Hz)` FRE, `COSPHI` COSPHI, `PF1` PF1, `PF2` PF2, `PF3` PF3, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2, `IAC3 (A)` IAC3, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWact1 (kW)` POWact1_avg, `POWact2 (kW)` POWact2_avg, `POWact3 (kW)` POWact3_avg, `POWact1 (kW)` POWact1_max, `POWact2 (kW)` POWact2_max, `POWact3 (kW)` POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

                $eje_aux = array("Lunes", "Martes", "Miércoles", "Jueves","Viernes", "Sabado", "Domingo");

                foreach ( $table_data as $i=>$dat ) {
                    $td_aux[$i]['eje'] = "$dat->eje";
                    $td_aux[$i]['date'] = "$dat->date";
                    $td_aux[$i]['time'] = "$dat->time";
                    $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                    $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                    $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                    $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                    $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                    $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                    $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                    $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                    $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                    $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                    $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                    $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                    $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                    $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');

                }

                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Semana Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Semana Anterior';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje,DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE DAYNAME(date) WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sabado' WHEN 'Sunday' THEN 'Domingo' END) eje, `date` date, DATE_FORMAT(time, '%H:%i') time, `FRE (Hz)` FRE, `COSPHI` COSPHI, `PF1` PF1, `PF2` PF2, `PF3` PF3, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2, `IAC3 (A)` IAC3, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWact1 (kW)` POWact1_avg, `POWact2 (kW)` POWact2_avg, `POWact3 (kW)` POWact3_avg, `POWact1 (kW)` POWact1_max, `POWact2 (kW)` POWact2_max, `POWact3 (kW)` POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

                $eje_aux = array("Lunes", "Martes", "Miércoles", "Jueves","Viernes", "Sabado", "Domingo");

                foreach ( $table_data as $i=>$dat ) {
                    $td_aux[$i]['eje'] = "$dat->eje";
                    $td_aux[$i]['date'] = "$dat->date";
                    $td_aux[$i]['time'] = "$dat->time";
                    $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                    $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                    $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                    $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                    $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                    $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                    $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                    $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                    $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                    $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                    $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                    $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                    $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                    $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                }

            break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Actual';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("DAY(date) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("DAY(date) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("DAY(date) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $eje_aux = date('d', strtotime($date_to));

                for ($i=0; $i < $eje_aux; $i++){
                    if($i < 9){
                        $td_aux[$i]['eje'] = str_pad($i+1, 2, '0', STR_PAD_LEFT);
                        $td_aux[$i]['date'] = date('Y-m-', strtotime($date_to)).$td_aux[$i]['eje'];
                        $td_aux[$i]['eje'] = $i+1;
                    }else{
                        $td_aux[$i]['eje'] = $i+1;
                        $td_aux[$i]['date'] = date('Y-m-', strtotime($date_to)).$td_aux[$i]['eje'];
                    }

                    foreach ( $table_data as $dat ) {
                        if($i+1 == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;

                            }
                        }
                    }
                }

                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Mes Anterior")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }
                $label_intervalo = 'Mes Anterior';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("DAY(date) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("DAY(date) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("DAY(date) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('date')->get();

                $eje_aux = date('d', strtotime($date_to));

                for ($i=0; $i < $eje_aux; $i++){
                    if($i < 9){
                        $td_aux[$i]['eje'] = str_pad($i+1, 2, '0', STR_PAD_LEFT);
                        $td_aux[$i]['date'] = date('Y-m-', strtotime($date_to)).$td_aux[$i]['eje'];
                        $td_aux[$i]['eje'] = $i+1;
                    }else{
                        $td_aux[$i]['eje'] = $i+1;
                        $td_aux[$i]['date'] = date('Y-m-', strtotime($date_to)).$td_aux[$i]['eje'];
                    }

                    foreach ( $table_data as $dat ) {
                        if($i+1 == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;
                            }
                        }
                    }
                }

                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Ultimo Trimestre")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    if($now == 1 || $now == 2 || $now == 3)
                    {
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                        }
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4 || $now == 7 || $now == 10){
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                        }
                        if($now == 4)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 7){
                            $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 10){
                            $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }
                    }elseif($now == 5 || $now == 8 || $now == 11){
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                        }
                        if($now == 5)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 8){
                            $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 11){
                            $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }
                    }elseif($now == 6 || $now == 9 || $now == 12){
                        if($dont == 0)
                        {
                            $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                            $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                        }
                        if($now == 6)
                        {
                            $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 9){
                            $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                        }elseif($now == 12){
                            $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                            $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        }
                    }
                }else{
                    // dd($now);
                    if($now == 1)
                    {
                        $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 4){
                        $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 7){
                        $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }elseif($now == 10){
                        $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                        $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    }
                }
                $label_intervalo = 'Ultimo Trimestre';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get()->toArray();
                for ($t=0; $t < 3; $t++)
                {
                    $values_graficas_[$t]['eje'] = $eje[$t];
                    $values_graficas_[$t]['potencia_activa_L1'] = 0;
                    $values_graficas_[$t]['potencia_activa_L2'] = 0;
                    $values_graficas_[$t]['potencia_activa_L3'] = 0;
                    foreach ($values_graficas as $val)
                    {
                        // dd($val);
                        $band = 1;
                        if(!empty($val) || !is_null($val))
                        {
                            if($val->eje == $eje[$t])
                            {
                                $values_graficas_[$t]['potencia_activa_L1'] = $val->potencia_activa_L1;
                                $values_graficas_[$t]['potencia_activa_L2'] = $val->potencia_activa_L2;
                                $values_graficas_[$t]['potencia_activa_L3'] = $val->potencia_activa_L3;
                                break;
                            }else{
                                $values_graficas_[$t]['potencia_activa_L1'] = 0;
                                $values_graficas_[$t]['potencia_activa_L2'] = 0;
                                $values_graficas_[$t]['potencia_activa_L3'] = 0;
                            }
                        }
                    }
                }

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 3; $t++) {
                    $values_corriente_[$t]['eje'] = $eje[$t];
                    $values_corriente_[$t]['corriente_L1'] = 0;
                    $values_corriente_[$t]['corriente_L2'] = 0;
                    $values_corriente_[$t]['corriente_L3'] = 0;
                    foreach ($values_corriente as $val)
                    {
                        $band = 1;
                        if(!empty($val) || !is_null($val))
                        {
                            if($val->eje == $eje[$t])
                            {
                                $values_corriente_[$t]['corriente_L1'] = $val->corriente_L1;
                                $values_corriente_[$t]['corriente_L2'] = $val->corriente_L2;
                                $values_corriente_[$t]['corriente_L3'] = $val->corriente_L3;
                                break;
                            }else{
                                $values_corriente_[$t]['corriente_L1'] = 0;
                                $values_corriente_[$t]['corriente_L2'] = 0;
                                $values_corriente_[$t]['corriente_L3'] = 0;
                            }
                        }
                    }
                }

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get()->toArray();


                for ($i=0; $i < 3; $i++){
                    $td_aux[$i]['eje'] = $eje[$i];


                    foreach ( $table_data as $dat ) {
                        if($eje[$i] == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;
                            }
                        }
                    }
                }

            break;

            case '10':
                $now = \Carbon\Carbon::now()->month;
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Trimestre Actual")
                {
                    $now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($now == 1 || $now == 2 || $now == 3)
                {
                    // $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Enero('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Febrero('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Marzo('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 4 || $now == 5 || $now == 6){
                    // $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Abril('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Mayo('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Junio('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 7 || $now == 8 || $now == 9){
                    // $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Julio('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Agosto('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Septiembre('.\Carbon\Carbon::parse($date_from)->year.')';
                }elseif($now == 10 || $now == 11 || $now == 12){
                    // $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                    // $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    if($dont == 0)
                    {
                        $date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
                        $date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
                    }
                    $eje[0] = 'Octubre('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[1] = 'Noviembre('.\Carbon\Carbon::parse($date_from)->year.')';
                    $eje[2] = 'Diciembre('.\Carbon\Carbon::parse($date_from)->year.')';
                }
                $label_intervalo = 'Trimestre Actual';
                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get()->toArray();
                for ($t=0; $t < 3; $t++)
                {
                    $values_graficas_[$t]['eje'] = $eje[$t];
                    $values_graficas_[$t]['potencia_activa_L1'] = 0;
                    $values_graficas_[$t]['potencia_activa_L2'] = 0;
                    $values_graficas_[$t]['potencia_activa_L3'] = 0;
                    foreach ($values_graficas as $val)
                    {
                        $band = 1;
                        if(!empty($val) || !is_null($val))
                        {
                            if($val->eje == $eje[$t])
                            {
                                $values_graficas_[$t]['eje'] = $eje[$t];
                                $values_graficas_[$t]['potencia_activa_L1'] = $val->potencia_activa_L1;
                                $values_graficas_[$t]['potencia_activa_L2'] = $val->potencia_activa_L2;
                                $values_graficas_[$t]['potencia_activa_L3'] = $val->potencia_activa_L3;
                                break;
                            }else{
                                $values_graficas_[$t]['potencia_activa_L1'] = 0;
                                $values_graficas_[$t]['potencia_activa_L2'] = 0;
                                $values_graficas_[$t]['potencia_activa_L3'] = 0;
                            }
                        }
                    }
                }

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get()->toArray();
                for ($t=0; $t < 3; $t++)
                {
                    $values_corriente_[$t]['eje'] = $eje[$t];
                    $values_corriente_[$t]['corriente_L1'] = 0;
                    $values_corriente_[$t]['corriente_L2'] = 0;
                    $values_corriente_[$t]['corriente_L3'] = 0;
                    foreach ($values_corriente as $val)
                    {
                        $band = 1;
                        if(!empty($val) || !is_null($val))
                        {
                            if($val->eje == $eje[$t])
                            {
                                $values_graficas_[$t]['eje'] = $eje[$t];
                                $values_corriente_[$t]['corriente_L1'] = $val->corriente_L1;
                                $values_corriente_[$t]['corriente_L2'] = $val->corriente_L2;
                                $values_corriente_[$t]['corriente_L3'] = $val->corriente_L3;
                                break;
                            }else{
                                $values_graficas_[$t]['eje'] = $eje[$t];
                                $values_corriente_[$t]['corriente_L1'] = 0;
                                $values_corriente_[$t]['corriente_L2'] = 0;
                                $values_corriente_[$t]['corriente_L3'] = 0;
                            }
                        }
                    }
                }

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get()->toArray();

                for ($i=0; $i < 3; $i++){
                    $td_aux[$i]['eje'] = $eje[$i];


                    foreach ( $table_data as $dat ) {
                        if($eje[$i] == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;
                            }
                        }
                    }
                }

            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Año")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                if($dont == 0)
                {
                    $eje[0] = "Enero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[1] = "Febrero(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[2] = "Marzo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[3] = "Abril(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[4] = "Mayo(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[5] = "Junio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[6] = "Julio(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[7] = "Agosto(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[8] = "Septiembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[9] = "Octubre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[10] = "Noviembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                    $eje[11] = "Diciembre(".\Carbon\Carbon::now()->subYears(1)->year.")";
                }else{
                    $a_o = \Carbon\Carbon::parse($date_from)->year;
                    $eje[0] = "Enero(".$a_o.")";
                    $eje[1] = "Febrero(".$a_o.")";
                    $eje[2] = "Marzo(".$a_o.")";
                    $eje[3] = "Abril(".$a_o.")";
                    $eje[4] = "Mayo(".$a_o.")";
                    $eje[5] = "Junio(".$a_o.")";
                    $eje[6] = "Julio(".$a_o.")";
                    $eje[7] = "Agosto(".$a_o.")";
                    $eje[8] = "Septiembre(".$a_o.")";
                    $eje[9] = "Octubre(".$a_o.")";
                    $eje[10] = "Noviembre(".$a_o.")";
                    $eje[11] = "Diciembre(".$a_o.")";
                }

                $label_intervalo = 'Último Año';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 12; $t++)
                {
                    $values_graficas_[$t]['eje'] = $eje[$t];
                    $values_graficas_[$t]['potencia_activa_L1'] = 0;
                    $values_graficas_[$t]['potencia_activa_L2'] = 0;
                    $values_graficas_[$t]['potencia_activa_L3'] = 0;
                    foreach ($values_graficas as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $values_graficas_[$t]['eje'] = $eje[$t];
                            $values_graficas_[$t]['potencia_activa_L1'] = $val->potencia_activa_L1;
                            $values_graficas_[$t]['potencia_activa_L2'] = $val->potencia_activa_L2;
                            $values_graficas_[$t]['potencia_activa_L3'] = $val->potencia_activa_L3;
                            break;
                        }else{
                            $values_graficas_[$t]['potencia_activa_L1'] = 0;
                            $values_graficas_[$t]['potencia_activa_L2'] = 0;
                            $values_graficas_[$t]['potencia_activa_L3'] = 0;
                        }
                    }
                }
                // \DB::raw('MONTH(date))'

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                for ($t=0; $t < 12; $t++)
                {
                    $values_corriente_[$t]['eje'] = $eje[$t];
                    $values_corriente_[$t]['corriente_L1'] = 0;
                    $values_corriente_[$t]['corriente_L2'] = 0;
                    $values_corriente_[$t]['corriente_L3'] = 0;
                    foreach ($values_corriente as $val)
                    {
                        if($val->eje == $eje[$t])
                        {
                            $values_corriente_[$t]['eje'] = $eje[$t];
                            $values_corriente_[$t]['corriente_L1'] = $val->corriente_L1;
                            $values_corriente_[$t]['corriente_L2'] = $val->corriente_L2;
                            $values_corriente_[$t]['corriente_L3'] = $val->corriente_L3;
                            break;
                        }else{
                            $values_corriente_[$t]['corriente_L1'] = 0;
                            $values_corriente_[$t]['corriente_L2'] = 0;
                            $values_corriente_[$t]['corriente_L3'] = 0;
                        }
                    }
                }

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                $a_o = \Carbon\Carbon::parse($date_from)->year;
                $eje[0] = "Enero(".$a_o.")";
                $eje[1] = "Febrero(".$a_o.")";
                $eje[2] = "Marzo(".$a_o.")";
                $eje[3] = "Abril(".$a_o.")";
                $eje[4] = "Mayo(".$a_o.")";
                $eje[5] = "Junio(".$a_o.")";
                $eje[6] = "Julio(".$a_o.")";
                $eje[7] = "Agosto(".$a_o.")";
                $eje[8] = "Septiembre(".$a_o.")";
                $eje[9] = "Octubre(".$a_o.")";
                $eje[10] = "Noviembre(".$a_o.")";
                $eje[11] = "Diciembre(".$a_o.")";

                for ($i=0; $i < 12; $i++){
                    $td_aux[$i]['eje'] = $eje[$i];


                    foreach ( $table_data as $dat ) {
                        if($eje[$i] == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;
                            }
                        }
                    }
                }
            break;

            case '11':
                $date_from = \Carbon\Carbon::now()->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfYear()->toDateString();
                $dont = 0;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Año Actual")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                    $dont = 1;
                }
                $label_intervalo = 'Año Actual';
                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                // \DB::raw('MONTH(date))'

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, DATE_FORMAT(time, '%H:%i') hora, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("(CASE MONTH(date) WHEN '01' THEN CONCAT('Enero(',YEAR(date),')') WHEN '02' THEN CONCAT('Febrero(',YEAR(date),')') WHEN '03' THEN CONCAT('Marzo(',YEAR(date),')') WHEN '04' THEN CONCAT('Abril(',YEAR(date),')') WHEN '05' THEN CONCAT('Mayo(',YEAR(date),')') WHEN '06' THEN CONCAT('Junio(',YEAR(date),')') WHEN '07' THEN CONCAT('Julio(',YEAR(date),')') WHEN '08' THEN CONCAT('Agosto(',YEAR(date),')') WHEN '09' THEN CONCAT('Septiembre(',YEAR(date),')') WHEN '10' THEN CONCAT('Octubre(',YEAR(date),')') WHEN '11' THEN CONCAT('Noviembre(',YEAR(date),')') WHEN '12' THEN CONCAT('Diciembre(',YEAR(date),')') END) eje, avg(`FRE (Hz)`) FRE, avg(`COSPHI`) COSPHI, avg(`PF1`) PF1, avg(`PF2`) PF2, avg(`PF3`) PF3, avg(`IAC1 (A)`) IAC1, avg(`IAC2 (A)`) IAC2, avg(`IAC3 (A)`) IAC3, avg(`POWrea1 (kVAr)`) POWrea1, avg(`POWrea2 (kVAr)`) POWrea2, avg(`POWrea3 (kVAr)`) POWrea3, avg(`POWact1 (kW)`) POWact1_avg, avg(`POWact2 (kW)`) POWact2_avg, avg(`POWact3 (kW)`) POWact3_avg, max(`POWact1 (kW)`) POWact1_max, max(`POWact2 (kW)`) POWact2_max, max(`POWact3 (kW)`) POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->orderBy(\DB::raw('MONTH(date)'),'ASC')->get();

                $a_o = \Carbon\Carbon::parse($date_from)->year;
                $eje[0] = "Enero(".$a_o.")";
                $eje[1] = "Febrero(".$a_o.")";
                $eje[2] = "Marzo(".$a_o.")";
                $eje[3] = "Abril(".$a_o.")";
                $eje[4] = "Mayo(".$a_o.")";
                $eje[5] = "Junio(".$a_o.")";
                $eje[6] = "Julio(".$a_o.")";
                $eje[7] = "Agosto(".$a_o.")";
                $eje[8] = "Septiembre(".$a_o.")";
                $eje[9] = "Octubre(".$a_o.")";
                $eje[10] = "Noviembre(".$a_o.")";
                $eje[11] = "Diciembre(".$a_o.")";


                for ($i=0; $i < 12; $i++){
                    $td_aux[$i]['eje'] = $eje[$i];


                    foreach ( $table_data as $dat ) {
                        if($eje[$i] == $dat->eje) {
                            $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                            $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                            $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                            $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                            $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                            $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                            $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                            $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                            $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                            $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                            $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                            $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                            $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                            $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                            $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                            $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                        }else{
                            if (!isset($td_Frecuencia[$i]['FRE'])) {
                                $td_PotAct_total[$i]['POWact_total'] = null;
                                $td_PotReac_total[$i]['POWrea_total'] = null;
                                $td_Frecuencia[$i]['FRE'] = null;
                                $td_PotAct_avg[$i]['POWact1_avg'] = null;
                                $td_PotAct_avg[$i]['POWact2_avg'] = null;
                                $td_PotAct_avg[$i]['POWact3_avg'] = null;
                                $td_PotAct_max[$i]['POWact1_max'] = null;
                                $td_PotAct_max[$i]['POWact2_max'] = null;
                                $td_PotAct_max[$i]['POWact3_max'] = null;
                                $td_PotReac[$i]['POWrea1'] = null;
                                $td_PotReac[$i]['POWrea2'] = null;
                                $td_PotReac[$i]['POWrea3'] = null;
                                $td_Intensidad[$i]['IAC1'] = null;
                                $td_Intensidad[$i]['IAC2'] = null;
                                $td_Intensidad[$i]['IAC3'] = null;
                                $td_FDP[$i]['PF1'] = null;
                                $td_FDP[$i]['PF2'] = null;
                                $td_FDP[$i]['PF3'] = null;
                                $td_COSPHI[$i]['COSPHI'] = null;
                            }
                        }
                    }
                }

            break;

            case '9':

                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("date eje, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("date eje, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("`date` date, DATE_FORMAT(time, '%H:%i') time, `FRE (Hz)` FRE, `COSPHI` COSPHI, `PF1` PF1, `PF2` PF2, `PF3` PF3, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2, `IAC3 (A)` IAC3, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWact1 (kW)` POWact1_avg, `POWact2 (kW)` POWact2_avg, `POWact3 (kW)` POWact3_avg, `POWact1 (kW)` POWact1_max, `POWact2 (kW)` POWact2_max, `POWact3 (kW)` POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('time')->orderBy('date')->orderBy('time')->get();

                foreach ( $table_data as $i=>$dat ) {
                    $td_aux[$i]['date'] = "$dat->date";
                    $td_aux[$i]['time'] = "$dat->time";
                    $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                    $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                    $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                    $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                    $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                    $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                    $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                    $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                    $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                    $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                    $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                    $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                    $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                    $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');
                }

            break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Hoy")
                {
                    $date_from = Session::get('_flash')['date_from_personalice'];
                    $date_to = Session::get('_flash')['date_to_personalice'];
                }

                $label_intervalo = 'Hoy';

                $values_graficas = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`POWact1 (kW)`) potencia_activa_L1, MAX(`POWact2 (kW)`) potencia_activa_L2, MAX(`POWact3 (kW)`) potencia_activa_L3"))->where('date',$date_from)->groupBy('time')->get();

                $values_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') eje, MAX(`IAC1 (A)`) corriente_L1, MAX(`IAC2 (A)`) corriente_L2, MAX(`IAC3 (A)`) corriente_L3"))->where('date',$date_from)->groupBy('time')->get();
                $values_graficas_ = $values_graficas;
                $values_corriente_ = $values_corriente;

                $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw("DATE_FORMAT(time, '%H:%i') time, `FRE (Hz)` FRE, `COSPHI` COSPHI, `PF1` PF1, `PF2` PF2, `PF3` PF3, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2, `IAC3 (A)` IAC3, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWact1 (kW)` POWact1_avg, `POWact2 (kW)` POWact2_avg, `POWact3 (kW)` POWact3_avg, `POWact1 (kW)` POWact1_max, `POWact2 (kW)` POWact2_max, `POWact3 (kW)` POWact3_max, `POWact_Total (kW)` POWact_total, `POWrea_Total (kVAr)` POWrea_total"))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

                foreach ( $table_data as $i=>$dat ) {
                    $td_aux[$i]['time'] = "$dat->time";
                    $td_PotAct_total[$i]['POWact_total'] = number_format($dat->POWact_total, 2, '.', '');
                    $td_PotReac_total[$i]['POWrea_total'] = number_format($dat->POWrea_total, 2, '.', '');
                    $td_Frecuencia[$i]['FRE'] = number_format($dat->FRE, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact1_avg'] = number_format($dat->POWact1_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact2_avg'] = number_format($dat->POWact2_avg, 2, '.', '');
                    $td_PotAct_avg[$i]['POWact3_avg'] = number_format($dat->POWact3_avg, 2, '.', '');
                    $td_PotAct_max[$i]['POWact1_max'] = number_format($dat->POWact1_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact2_max'] = number_format($dat->POWact2_max, 2, '.', '');
                    $td_PotAct_max[$i]['POWact3_max'] = number_format($dat->POWact3_max, 2, '.', '');
                    $td_PotReac[$i]['POWrea1'] = number_format($dat->POWrea1, 2, '.', '');
                    $td_PotReac[$i]['POWrea2'] = number_format($dat->POWrea2, 2, '.', '');
                    $td_PotReac[$i]['POWrea3'] = number_format($dat->POWrea3, 2, '.', '');
                    $td_Intensidad[$i]['IAC1'] = number_format($dat->IAC1, 2, '.', '');
                    $td_Intensidad[$i]['IAC2'] = number_format($dat->IAC2, 2, '.', '');
                    $td_Intensidad[$i]['IAC3'] = number_format($dat->IAC3, 2, '.', '');
                    $td_FDP[$i]['PF1'] = number_format($dat->PF1, 2, '.', '');
                    $td_FDP[$i]['PF2'] = number_format($dat->PF2, 2, '.', '');
                    $td_FDP[$i]['PF3'] = number_format($dat->PF3, 2, '.', '');
                    $td_COSPHI[$i]['COSPHI'] = number_format($dat->COSPHI, 2, '.', '');

                }
            break;
        }
		    $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw('`date` date, `time` time,`POWact1 (kW)` POWact1, `POWact2 (kW)` POWact2, `POWact3 (kW)` POWact3, `POWact_Total (kW)` POWact_Total, `POWapp1 (kVA)` POWapp1, `POWapp2 (kVA)` POWapp2, `POWapp3 (kVA)` POWapp3, `POWapa_Total (kVA)` POWapa_Total, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWrea_Total (kVAr)` POWrea_Total, `PF1` PF1, `PF2` PF2, `PF3` PF3, `PF_Total` PF_Total, `FRE (Hz)` FRE, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2 , `IAC3 (A)` IAC3, `VAC1 (V)` VAC1, `VAC2  (V)` VAC2, `VAC3  (V)` VAC3, `VAC  (V)` VAC, `COSPHI` COSPHI,`THDV1(%)` THDV1, `THDV2(%)` THDV2, `THDV3(%)` THDV3, `THDI1(%)` THDI1, `THDI2(%)` THDI2, `THDI3(%)` THDI3'))->where('date','>=',$date_from)->where('date','<=',$date_to)->get();

        $total_energias = $db->table('Analizadores_Tipo')->select(\DB::raw("SUM(`ENEact (kWh)`) energia_activa, SUM(`ENErea (kVArh)`) energia_reactiva"))->where('date','>=',$date_from)->where('date','<=',$date_to)->first();

        $datos_analizador_corriente = $db->table('Corrientes_Por_Fase')->select(\DB::raw("date, time, (`IAC1 (A)`) corriente_L1,
                                                (`IAC2 (A)`) corriente_L2, (`IAC3 (A)`) corriente_L3"))
                                            ->where('date','>=',$date_from)->where('date','<=',$date_to)->orderBy('date')->get();
        $datos_analizador_potencia = $db->table('Potencia_Activa_Por_Fase')->select(\DB::raw("date, time, (`POWact1 (kW)`) potencia_activa_L1, (`POWact2 (kW)`) potencia_activa_L2, (`POWact3 (kW)`) potencia_activa_L3"))->where('date','>=',$date_from)->where('date','<=',$date_to)->orderBy('date')->get();
        $total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0; $total5 = 0; $total6 = 0;

        foreach ($datos_analizador_potencia as $value) {
            $total1 += $value->potencia_activa_L1;
            $value->potencia_activa_L1 = number_format($value->potencia_activa_L1,0,',','.');
            $total2 += $value->potencia_activa_L2;
            $value->potencia_activa_L2 = number_format($value->potencia_activa_L2,0,',','.');
            $total3 += $value->potencia_activa_L3;
            $value->potencia_activa_L3 = number_format($value->potencia_activa_L3,0,',','.');
        }

        foreach ($datos_analizador_corriente as $key) {
            $total4 += $key->corriente_L1;
            $key->corriente_L1 = number_format($key->corriente_L1,0,',','.');
            $total5 += $key->corriente_L2;
            $key->corriente_L2 = number_format($key->corriente_L2,0,',','.');
            $total6 += $key->corriente_L3;
            $key->corriente_L3 = number_format($key->corriente_L3,0,',','.');
        }
        if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Area_Cliente' AND column_name = '`LOGOTIPO`'")->first())
        {
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        }

        \DB::disconnect('mysql2');

        $analyzer_energy = AnalyzerController::getDataAnalyzer($analizador->id, $date_from, $date_to);
        $analyzers_data = AnalyzerController::getAnalyzerGroupDetails($group_id, $user_id, $contador2);



        $titulo = "Resumen Diario";
        $tipo_count = $aux_contador->tipo;
        $contador_label = $aux_contador->count_label;
        $contador_id = $aux_contador->id;
        $id = $user_id;
        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$user_id)->get()->first();
            if(Auth::user()->tipo != 1)
            {
                $ctrl = 0;
            }
            else
            {
                $ctrl = 1;
            }

            if(is_null($user->_perfil))
            {
                $direccion = 'sin ubicación';
            }
            else
            {
                $direccion = $user->_perfil->direccion;
            }

            return view('analizadores.analizador_graficas',compact('user','titulo','id','ctrl','values_graficas',
                'values_corriente','label_intervalo','date_from','date_to','direccion','tipo_count',
                'contador_label','analizador','total_energias','datos_analizador_potencia',
                'datos_analizador_corriente','total1','total2','total3','total4','total5','total6',
                'color_etiqueta','contador_id','domicilio','dir_image_count','tipo_tarifa','values_corriente_',
                'values_graficas_','eje','table_data', 'contador2',
                'analyzer_energy', 'analyzers_data', 'group_id','informes_programados','alertas_programados',
                'td_Frecuencia','td_COSPHI','td_FDP','td_Intensidad','td_PotReac','td_PotAct_avg','td_PotAct_max','td_aux', 'td_PotAct_total', 'td_PotReac_total'
                ));
        }
        return \Redirect::to('https://submeter.es/');
        // return view('analizadores.analizador_graficas',compact('user','titulo','id','ctrl','values_graficas','values_corriente','label_intervalo','date_from','date_to','direccion','tipo_count','contador_label','analizador','total_energias','datos_analizador_potencia','datos_analizador_corriente','total1','total2','total3','total4','total5','total6','color_etiqueta','contador_id','domicilio','dir_image_count','tipo_tarifa'));
    }

    function exportCSVAnalizador(Request $request)
    {
        $analizador = Analizador::where('id',$request->analizador_id)->first();
        config(['database.connections.mysql2.host' => $analizador->host]);
        config(['database.connections.mysql2.port' => $analizador->port]);
        config(['database.connections.mysql2.database' => $analizador->database]);
        config(['database.connections.mysql2.username' => $analizador->username]);
        config(['database.connections.mysql2.password' => $analizador->password]);
        env('MYSQL2_HOST',$analizador->host);
        env('MYSQL2_DATABASE',$analizador->database);
        env('MYSQL2_USERNAME', $analizador->username);
        env('MYSQL2_PASSWORD',$analizador->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $table_data = $db->table('Analizadores_Tipo')->select(\DB::raw('`date` date, `time` time,`ENEact (kWh)` ENEact,`ENEapa (kVAh)` ENEapa, `ENErea_Ind (kVArh)` ENErea_Ind,`ENErea_Cap (kVArh)` ENErea_Cap,`ENErea (kVArh)` ENErea,`POWact1 (kW)` POWact1, `POWact2 (kW)` POWact2, `POWact3 (kW)` POWact3, `POWact_Total (kW)` POWact_Total, `POWapp1 (kVA)` POWapp1, `POWapp2 (kVA)` POWapp2, `POWapp3 (kVA)` POWapp3, `POWapa_Total (kVA)` POWapa_Total, `POWrea1 (kVAr)` POWrea1, `POWrea2 (kVAr)` POWrea2, `POWrea3 (kVAr)` POWrea3, `POWrea_Total (kVAr)` POWrea_Total, `PF1` PF1, `PF2` PF2, `PF3` PF3, `PF_Total` PF_Total, `FRE (Hz)` FRE, `IAC1 (A)` IAC1, `IAC2 (A)` IAC2 , `IAC3 (A)` IAC3, `VAC1 (V)` VAC1, `VAC2  (V)` VAC2, `VAC3  (V)` VAC3, `VAC  (V)` VAC, `COSPHI` COSPHI,`THDV1(%)` THDV1, `THDV2(%)` THDV2, `THDV3(%)` THDV3, `THDI1(%)` THDI1, `THDI2(%)` THDI2, `THDI3(%)` THDI3'))->where('date','>=',$request->date_from)->where('date','<=',$request->date_to)->orderBy('date')->orderBy('time')->get();

        $filename = "Datos_".$analizador->label.".csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Fecha', 'Tiempo', 'ENEact (kWh)', 'ENEapa (kVAh)', 'ENErea_Ind (kVArh)', 'ENErea_Cap (kVArh)', 'ENErea (kVArh)', 'POWact1 (kW)', 'POWact2 (kW)','POWact3 (kW)','POWact_Trifasico (kW)','POWapp1 (kVA)','POWapp2 (kVA)','POWapp3 (kVA)','POWapp_Trifasico (kVA)','POWrea1 (kVAr)','POWrea2 (kVAr)','POWrea3 (kVAr)','POWrea_Trifasico (kVAr)','PF1','PF2','PF3','PF_Trifasico','FRE (Hz)','IAC1 (A)','IAC2 (A)','IAC3 (A)','VAC1 (V)','VAC2 (V)','VAC3 (V)','VAC_Trifasico (V)','COSPHI','THDV1(%)','THDV2(%)','THDV3(%)','THDI1(%)','THDI2(%)','THDI3(%)'),';');
        $i = 0;
        foreach($table_data as $data) {
            fputcsv($handle, array(
              $data->date, $data->time,
            number_format($data->ENEact,3,',','.'),
            number_format($data->ENEapa,3,',','.'),
            number_format($data->ENErea_Ind,3,',','.'),
            number_format($data->ENErea_Cap,3,',','.'),
            number_format($data->ENErea,3,',','.'),
            number_format($data->POWact1,3,',','.'),
            number_format($data->POWact2,3,',','.'),
            number_format($data->POWact3,3,',','.'),
            number_format($data->POWact_Total,3,',','.'),
            number_format($data->POWapp1,3,',','.'),
            number_format($data->POWapp2,3,',','.'),
            number_format($data->POWapp3,3,',','.'),
            number_format($data->POWapa_Total,3,',','.'),
            number_format($data->POWrea1,3,',','.'),
            number_format($data->POWrea2,3,',','.'),
            number_format($data->POWrea3,3,',','.'),
            number_format($data->POWrea_Total,3,',','.'),
            number_format($data->PF1,3,',','.'),
            number_format($data->PF2,3,',','.'),
            number_format($data->PF3,3,',','.'),
            number_format($data->PF_Total,3,',','.'),
            number_format($data->FRE,3,',','.'),
            number_format($data->IAC1,3,',','.'),
            number_format($data->IAC2,3,',','.'),
            number_format($data->IAC3,3,',','.'),
            number_format($data->VAC1,3,',','.'),
            number_format($data->VAC2,3,',','.'),
            number_format($data->VAC3,3,',','.'),
            number_format($data->VAC,3,',','.'),
            number_format($data->COSPHI,3,',','.'),
            number_format($data->THDV1,3,',','.'),
            number_format($data->THDV2,3,',','.'),
            number_format($data->THDV3,3,',','.'),
            number_format($data->THDI1,3,',','.'),
            number_format($data->THDI2,3,',','.'),
            number_format($data->THDI3,3,',','.')
          ),';');

            $i++;
        }

        fclose($handle);

        $headers = array(
                        'Content-Type' => 'text/csv',
        );
        // if($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first())
        //     $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        // else
        //     $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        return Response::download($filename, $filename, $headers);
        \DB::disconnect('mysql2');
    }

    function Produccion($id,Request $request)
    {
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        $contador_label = $contador2->count_label;

        $interval = Session::get('_flash')['intervalos'];

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }elseif($now == 4 || $now == 7 || $now == 10){
                    $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                }elseif($now == 5 || $now == 8 || $now == 11){
                    $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                }elseif($now == 6 || $now == 9 || $now == 12){
                    $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                }
                $label_intervalo = 'Último Trimestre';
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $label_intervalo = 'Último Año';
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Hoy';
                break;
        }
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');
        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
                else
                    $ctrl = 1;

                    return view('produccion.produccion',compact('user','titulo','id','label_intervalo','ctrl','contador_label','tipo_count','domicilio','dir_image_count'));
        }
        return \Redirect::to('https://submeter.es/');
    }

    function Identificadores($id,Request $request)
    {
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));

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


        $tipo_count = $contador2->tipo;
        $tipo_tarifa = $contador2->tarifa;
        $contador_label = $contador2->count_label;
        $current_count = $contador_label;

        config(['database.connections.mysql2.host' => $contador2->host]);
        config(['database.connections.mysql2.port' => $contador2->port]);
        config(['database.connections.mysql2.database' => $contador2->database]);
        config(['database.connections.mysql2.username' => $contador2->username]);
        config(['database.connections.mysql2.password' => $contador2->password]);
        env('MYSQL2_HOST',$contador2->host);
        env('MYSQL2_DATABASE',$contador2->database);
        env('MYSQL2_USERNAME', $contador2->username);
        env('MYSQL2_PASSWORD',$contador2->password);
        try {
            \DB::connection('mysql2')->getPdo();
        } catch (\Exception $e) {
            Session::flash('message-error', "La base de datos a la que se desea conectar está mal configurada. Por favor, edite los parámetros de configuración de conexión.");
            return \Redirect::back();
        }
        $db = \DB::connection('mysql2');

        $domicilio = $db->table('Area_Cliente')->select(\DB::raw('`DENOMINACIÓN SOCIAL` denominacion_social, `SOCIAL DOMICILIO` social_domicilio, `SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio, CUPS, CIF, `DISTRIBUIDORA EMPRESA` distribuidora_empresa, `COMERCIALIZADORA EMPRESA` comercializadora_empresa, `CONTACTO DE  PERSONA` contacto_persona, `TELÉFONO`, `CONTACTO O  AYUDA` contacto_ayuda, `TARIFA`'))->first();

        $contador_label = $contador2->count_label;

        $interval = Session::get('_flash')['intervalos'];

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Ayer';
                break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Actual';
                break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Anterior';
                break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Actual';
                break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Anterior';
                break;

            case '7':
                $now = \Carbon\Carbon::now()->month;
                if($now == 1 || $now == 2 || $now == 3)
                {
                    $date_from = \Carbon\Carbon::now()->subMonths($now+2)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths($now)->endOfMonth()->toDateString();
                }elseif($now == 4 || $now == 7 || $now == 10){
                    $date_from = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                }elseif($now == 5 || $now == 8 || $now == 11){
                    $date_from = \Carbon\Carbon::now()->subMonths(4)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
                }elseif($now == 6 || $now == 9 || $now == 12){
                    $date_from = \Carbon\Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                    $date_to = \Carbon\Carbon::now()->subMonths(3)->endOfMonth()->toDateString();
                }
                $label_intervalo = 'Último Trimestre';
                break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $label_intervalo = 'Último Año';
                break;

            case '9':
                $date_from = Session::get('_flash')['date_from_personalice'];
                $date_to = Session::get('_flash')['date_to_personalice'];
                $label_intervalo = 'Personalizado';
                // dd($date_from,$date_to);
                break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Hoy';
                break;
        }
        if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()))
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
        else
            $dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
        \DB::disconnect('mysql2');
        if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1)
        {
            $user = User::where('id',$id)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
                else
                    $ctrl = 1;

                    return view('identificadores.identificadores',compact('user','titulo','id','label_intervalo','ctrl','contador_label','tipo_count','domicilio','dir_image_count'));
        }
        return \Redirect::to('https://submeter.es/');
    }
}
