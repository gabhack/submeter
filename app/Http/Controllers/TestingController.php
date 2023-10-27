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
use App\Http\Controllers\StatisticsApiController;
use App\Mail\AlertaGeneral;
use App\AlertasGeneral;

class TestingController extends Controller
{
	protected $intervalo;

	//Solo Usuario Logedo
	public function __construct()
	{
		$this->middleware('auth');
	}

	function index(){
        $alertas = \DB::select("SELECT * FROM alertas_general WHERE activado = 1");
        $alertas = json_decode(json_encode($alertas), TRUE);
        if(count($alertas) > 0){
            $tmonth = (int)(date('m'));
            $tday = date('w');
            foreach($alertas as $alerta){
                $conexion = json_decode($alerta['conexion'], TRUE);
                $frecuencia_mes = explode(',', $alerta['frecuencia_mes']);
                $frecuencia_dia = explode(',', $alerta['frecuencia_dia']);

                if($conexion) {
                    $hasNotification = FALSE;
                    if($frecuencia_mes && count($frecuencia_mes)>0) {
                        foreach($frecuencia_mes as $fmes) {
                            if($tmonth == ($fmes+1)) {
                                foreach($frecuencia_dia as $fdia) {
                                    if($tday == ($fdia+1)) {
                                        $hasNotification = TRUE;
                                    }
                                }
                            }
                        }
                    }
                    if($hasNotification) {
                        $avisos = (int)$alerta['avisos'];
                        if($avisos > 0 && !is_null($alerta['control_avisos_fecha']) && !is_null($alerta['control_avisos_num'])) {
                            if(date('Y-m-d') === $alerta['control_avisos_fecha']) {
								$avisos_json = json_decode($alerta['control_avisos_num'], TRUE);
                                if($avisos <= count($avisos_json)) {
                                    $hasNotification = FALSE;
                                }
                            }
                        }
                        if($hasNotification){
                            $this->process_alert($alerta, $conexion);
                        }
                    }
                }
            }
        }
	}

    private function process_alert($alerta, $conexion) {
		$contadorPrev = EnergyMeter::where('id', $conexion['meter_id'])->first();
		if(!$contadorPrev) return false;

		$dbList = $contadorPrev->production_databases;
		if(!$dbList || count($dbList)==0) return false;

		$contadorObj = null;
		foreach($dbList as $db){
			if($db['name'] == $conexion['conection_name']){
				$contadorObj = $db;
			}
		}
		if(!$contadorObj) return false;

        $alerta["destinatarios"] = preg_replace('/\s+/S', "", $alerta["destinatarios"]);
		config(['database.connections.mysql2.host' => $contadorObj['host']]);
		config(['database.connections.mysql2.port' => $contadorObj['port']]);
		config(['database.connections.mysql2.database' => $contadorObj['database']]);
		config(['database.connections.mysql2.username' => $contadorObj['username']]);
		config(['database.connections.mysql2.password' => $contadorObj['password']]);
		$db = \DB::connection('mysql2');

		$str_condition = '=';
		switch($conexion['condition']){
			case '0': 
				$str_condition = '>=';
				break;
			case '1': 
				$str_condition = '<=';
				break;
			case '2': 
				$str_condition = '=';
				break;
		}

		$limitQuery = 10;
		$current_date = date('Y-m-d');
		$current_hour = date('H:i');
		$prev_filter_a = [];
		$newArrayHours = [];
		$current_iteration = 1;

		if(!is_null($alerta['control_avisos_fecha']) && !is_null($alerta['control_avisos_num'])) {
			if($current_date === $alerta['control_avisos_fecha']) {
				$avisos_json = json_decode($alerta['control_avisos_num'], TRUE);
				$current_iteration = count($avisos_json) + 1;
				foreach($avisos_json as $aviso){
					$prev_filter_b = $db->table($conexion['table_name'])
					->select(\DB::raw("`date`, `time`, `".$conexion['field_name']."`"))
					->where([
						[$conexion['field_name'], $str_condition, $conexion['limit']], 
						['date', '=', $current_date], 
						['time', 'like', $aviso.'%']
					])
					->first();
					if($prev_filter_b && !in_array($aviso, $newArrayHours)){
						$prev_filter_a[] = $prev_filter_b;
						$newArrayHours[] = $aviso;
					}
				}
			}
		}
		echo $current_hour."<br><br>";
		$current_hour = '19:20';

		$prev_filter_c = $db->table($conexion['table_name'])
		->select(\DB::raw("`date`, `time`, `".$conexion['field_name']."`"))
		->where([
			[$conexion['field_name'], $str_condition, $conexion['limit']], 
			['date', '=', $current_date], 
			['time', 'like', $current_hour.'%']
		])
		->first();
		if($prev_filter_c && !in_array($current_hour, $newArrayHours)){
			$prev_filter_a[] = $prev_filter_c;
			$newArrayHours[] = $current_hour;
		}

		$filters = count($prev_filter_a) === $current_iteration ? $prev_filter_a : [];
		if($filters && count($filters)>0){
			AlertasGeneral::where('id', $alerta['id'])->update([
				'control_avisos_fecha' => $current_date, 
				'control_avisos_num' => count($newArrayHours)>0 ? json_encode($newArrayHours) : NULL
			]);
			$mails = explode(';', $alerta['destinatarios']);
			foreach ($mails as $mail) {
				$dataMail = [];
				$dataMail['empresa'] = $conexion['enterprise_name'];
				$dataMail['contador'] = $conexion['meter_name'];
				$dataMail['nombre_alerta'] = $alerta['nombre_alerta'];
				$dataMail['base_datos'] = $conexion['conection_name'];
				$dataMail['nombre_campo'] = $conexion['field_name'];
				$dataMail['consigna'] = $conexion['field_name']." ".$str_condition." ".$conexion['limit'];
				$dataMail['filters'] = $filters;
				Mail::to($mail,'Submeter 4.0 ('.$alerta['nombre_alerta'].')')->send(new AlertaGeneral($dataMail));
				echo "sending mail...<br><br>";
				sleep(3);
			}
		}
		\DB::disconnect('mysql2');
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
			$porcentajes_alerta = [4=>'0', 5=>'5%', 6=>'10%', 7=>'20%', 8=>'25%', 9=>'30%', 10=>'40%', 1 => '50%', 2 => '70%', 3=>'90%'];
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
			$alertas_general = \DB::select("SELECT * FROM alertas_general WHERE user_id = ".$id." AND contador = '".$contador_label."'");
			return view('informes_periodicos_alertas.informes_periodicos_alertas',compact('user', 'titulo', 'cliente', 'id', 'ctrl','label_intervalo','date_from', 'date_to','cont', 'informes_programados', 'informes_analizadores_programados', 'alertas_programadas', 'tipo_count', 'contador_label', 'dir_image_count', 'tipo_tarifa', 'porcentajes_alerta', 'search_types', 'alertas_general'));
		}
		return \Redirect::to('https://submeter.es/');
		// return view('informes_periodicos_alertas.informes_periodicos_alertas',compact('user','titulo','cliente','id','ctrl','label_intervalo','date_from', 'date_to','cont','informes_programados','alertas_programadas','tipo_count','contador_label','dir_image_count','tipo_tarifa'));
	}
}
