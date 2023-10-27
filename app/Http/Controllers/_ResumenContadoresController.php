<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Mail\CreateClienteMail as CreateClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\PerfilUserRequest;
use App\Jobs\SendEmailJob;
use App\Http\Requests\UsuarioRegistradoRequest;
use App\User;
use App\Informes;
use App\Alertas;
use App\User2;
use App\Perfil;
use App\Count;
use App\CurrentCount;
use App\Analizador;
use App\intervalos_user;
use Session;
use Validator;
use Auth;
use File;
use PDF;
use Response;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;



class ResumenContadoresController extends Controller
{
	// function VerPanelUser($id,$ctrl)
	function VerPanelUser($id)
	{
		// id representa el id del usuario que se desea ver  y $ctrl el control que indica que
		// la vista mostrada viene del panel administrativo

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
		$contador_name = array();
		$contador_cups = array();
		$contador_direccion = array();
		$contador_tarifa = array();

		$titulo = 'Resumen de Contadores';
		$hoy = \Carbon\Carbon::now();


		switch ($interval) {
			case '2':
				$date_from = \Carbon\Carbon::now()->toDateString();
				$date_to = $date_from;
				if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Hoy")
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
				if(isset(Session::get('_flash')['label_intervalo_navigation']) && Session::get('_flash')['label_intervalo_navigation'] == "Último Trimestre")
				{
					$now = \Carbon\Carbon::parse(Session::get('_flash')['date_from_personalice'])->addMonth(1)->month;
					$date_from = Session::get('_flash')['date_from_personalice'];
					$date_to = Session::get('_flash')['date_to_personalice'];
					$dont = 1;
				}
				if($now == 4 || $now == 5 || $now == 6)
				{
					if($dont == 0)
					{
						$date_from = \Carbon\Carbon::create(null, 1, 1, 0)->toDateString();
						$date_to = \Carbon\Carbon::now()->addMonths(3-$now)->endOfMonth()->toDateString();
					}
					$eje[0] = 'Enero('.\Carbon\Carbon::now()->year.')';
					$eje[1] = 'Febrero('.\Carbon\Carbon::now()->year.')';
					$eje[2] = 'Marzo('.\Carbon\Carbon::now()->year.')';
				}elseif($now == 7 || $now == 8 || $now == 9){
					if($dont == 0)
					{
						$date_from = \Carbon\Carbon::create(null, 4, 1, 0)->toDateString();
						$date_to = \Carbon\Carbon::now()->addMonths(6-$now)->endOfMonth()->toDateString();
					}
					$eje[0] = 'Abril('.\Carbon\Carbon::now()->year.')';
					$eje[1] = 'Mayo('.\Carbon\Carbon::now()->year.')';
					$eje[2] = 'Junio('.\Carbon\Carbon::now()->year.')';
				}elseif($now == 10 || $now == 11 || $now == 12){
					if($dont == 0)
					{
						$date_from = \Carbon\Carbon::create(null, 7, 1, 0)->toDateString();
						$date_to = \Carbon\Carbon::now()->addMonths(9-$now)->endOfMonth()->toDateString();
					}
					$eje[0] = 'Julio('.\Carbon\Carbon::now()->year.')';
					$eje[1] = 'Agosto('.\Carbon\Carbon::now()->year.')';
					$eje[2] = 'Septiembre('.\Carbon\Carbon::now()->year.')';
				}elseif($now == 1 || $now == 2 || $now == 3){
					if($dont == 0)
					{
						$date_from = \Carbon\Carbon::create(null, 10, 1, 0)->toDateString();
						$date_to = \Carbon\Carbon::now()->addMonths(12-$now)->endOfMonth()->toDateString();
					}
					$eje[0] = 'Octubre('.\Carbon\Carbon::now()->year.')';
					$eje[1] = 'Noviembre('.\Carbon\Carbon::now()->year.')';
					$eje[2] = 'Diciembre('.\Carbon\Carbon::now()->year.')';
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

		$countsArray = $user->energy_meters;

		$total_termino_potencia=array();
		$total_potencia_contratada=array();
		$total_excesos_potencia=array();
		$IVA=array();
		$markers = [];

		$contadoresGas = [];
		$porcentajeIva = 0.21;
		$iterations = 0;
		$totalContadoresGas = [
			"variable" 	=> 0,
			"fijo" 			=> 0,
			"consumo" 	=> 0,
			"iehc" 			=> 0,
			"alquiler"	=> 0,
			"iva" 			=> 0,
			"total" 		=> 0
		];

		foreach ($countsArray as $index => $cont) {
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

			$domicilio = $db->table('Area_Cliente')->select(\DB::raw('
				`DENOMINACIÓN SOCIAL` denominacion_social,
				`SOCIAL DOMICILIO` social_domicilio,
				`SUMINISTRO DEL  DOMICILIO` suministro_del_domicilio,
				`CUPS` cups,
				`CIF` cif,
				`DISTRIBUIDORA EMPRESA` distribuidora_empresa,
				`COMERCIALIZADORA EMPRESA` comercializadora_empresa,
				`CONTACTO DE  PERSONA` contacto_persona,
				`TELÉFONO` telefono,
				`CONTACTO O  AYUDA` contacto_ayuda,
				`TARIFA` tarifa,
				`LATITUD` latitud,
				`LONGITUD` longitud
			'))->first();

			array_push($contador_name,$cont['count_label']);
			array_push($contador_cups,$domicilio->cups);
			array_push($contador_tarifa, $domicilio->tarifa );

			$addressDescription = $domicilio->suministro_del_domicilio;
			#$coords = $this->getLatLong( urlencode( $addressDescription ) );

			$indexForLabel = $index + 1;
			$addressInfo = [
				'name'          => $cont['count_label'],
				'custom_label'  => "C$indexForLabel",
				'label'         => substr( $addressDescription, 0, 1 ),
				'address'       => urldecode( $addressDescription ),
				'lat'           => isset( $domicilio->latitud ) ? $domicilio->latitud : null,
				'lng'           => isset( $domicilio->longitud ) ? $domicilio->longitud : null,
			];

			array_push($markers, $addressInfo );
			array_push($contador_direccion, $addressDescription );


			if($cont['tipo'] == 1 OR $cont['tipo'] == 2){

				// CODIGO NUEVO INICIO

				$potencia_contratada_2 = $db->table('Potencia_Contratada')
					->select(\DB::raw("Periodo as periodo, MAX(`Potencia_contratada`) as potencia_contratada,
										  RIGHT(Periodo,1) as periodo_int"))
					->where('date_start','<=',$date_from)->orWhere('date_end','>=',$date_to)
					->groupBy('Periodo')->get();

				$arreglo_potencia = array();
				$vector_potencia = array();
				foreach($potencia_contratada_2 as $potencia){
					$arreglo_potencia[] = array("periodo"=>$potencia->periodo, "potencia"=>$potencia->potencia_contratada);
					$idx_periodo = intval($potencia->periodo_int) - 1;
					$vector_potencia[$idx_periodo] = doubleval($potencia->potencia_contratada);
				}

				$potencia_contratada_2_optima = $db->table('Potencia_Contratada_Optima')
					->select(\DB::raw("Periodo as periodo, `Potencia_contratada` as potencia_contratada,
												  RIGHT(Periodo,1) as periodo_int"))
					->get();

				$arreglo_potencia_optima = array();
				$vector_potencia_optima = array();
				foreach($potencia_contratada_2_optima as $potencia){
					$arreglo_potencia_optima[] = array("periodo"=>$potencia->periodo, "potencia"=>$potencia->potencia_contratada);
					$idx_periodo = intval($potencia->periodo_int) - 1;
					$vector_potencia_optima[$idx_periodo] = doubleval($potencia->potencia_contratada);
				}

				$precios_potencia = $db->table("ZPI_Precio_Potencia_Contratada")
					->select(\DB::raw("RIGHT(Periodo,1) as periodo, Precio as precio"))
					->orderBy("Periodo")->get();

				$vector_costos = array();
				foreach ($precios_potencia as $precio)
				{
					$idx = intval($precio->periodo) - 1;
					$vector_costos[$idx] = 12*floatval($precio->precio) / 365;
				}

				$data_contador = $db->table("ZPI_Contador_Festivos_Periodos")
					->select(\DB::raw("MONTH(date) AS month, YEAR(date) AS year, 4*`EAct imp(kWh)` AS potencia,
										  RIGHT(Periodo,1) as periodo, DATEDIFF(date, '1970-01-01') AS days_unix, date, time"))
					->where("date", ">=", $date_from)
					->where("date", "<=", $date_to)
					->get()->toArray();

				$data_calculos = compact("vector_potencia", "vector_potencia_optima", "data_contador", "vector_costos",
					"date_from", "date_to", "interval");

				$objAnalisis = new AnalisisPotencia();
				if($cont['tarifa'] == 1) {
					$data_analisis = $objAnalisis->calcularCostos6($data_calculos);

					$analisis_potencia_contratada = $data_analisis["totalFC"];
					$analisis_excesos_potencia = $data_analisis["totalFPE"];

				} else {

					$data_analisis = $objAnalisis->calcularCostos3($data_calculos);

				}
				$analisis_termino_potencia = $data_analisis["totalFP"];

				if($cont['tarifa'] == 1) {

					array_push($total_potencia_contratada,$analisis_potencia_contratada);
					array_push($total_excesos_potencia,$analisis_excesos_potencia);

				}else {
					array_push($total_potencia_contratada,$analisis_termino_potencia);
					array_push($total_excesos_potencia,0);
				}

				array_push($total_termino_potencia,$analisis_termino_potencia);



				// CODIGO NUEVO FIN

			}

			if($cont['tipo'] < 3 && $cont['tarifa'] == 1){
				$db_coste_activa = $db->table('Coste_Energia_Activa')->select(\DB::raw("
					SUM(P1) costeP1,
					SUM(P2) costeP2,
					SUM(P3) costeP3,
					SUM(P4) costeP4,
					SUM(P5) costeP5,
					SUM(P6) costeP6
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->get()->toArray();

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

				$coste_activa[] = $aux_coste_activa;


				// $coste_activa = floatval(\DB::select("SELECT SUM(`Coste Energia Activa (€)`) valor FROM ".$contador.".coste_energia_activa WHERE date >='".$date_from."' AND   date<= '".$date_to."'")[0]->valor);

				$db_coste_reactiva = ($db->table('Coste_Energia_Reactiva')->select(\DB::raw("
					SUM(P1) costeP1,
					SUM(P2) costeP2,
					SUM(P3) costeP3,
					SUM(P4) costeP4,
					SUM(P5) costeP5,
					SUM(P6) costeP6
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->get()->toArray());

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

				$db_potencia_contratada = ($db->table('Coste_Potencia_Contratada')->select(\DB::raw("
					SUM(P1) costeP1,
					SUM(P2) costeP2,
					SUM(P3) costeP3,
					SUM(P4) costeP4,
					SUM(P5) costeP5,
					SUM(P6) costeP6
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->get()->toArray());

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

				if($cont['database'] == 'Prueba_Contador_6.0_V3'){
					$db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("
						SUM(P1) costeP1,
						SUM(P2) costeP2,
						SUM(P3) costeP3,
						SUM(P4) costeP4,
						SUM(P5) costeP5,
						SUM(P6) costeP6"
					))
						->where('date_start','<=',$date_from)
						->where('date_end','>=',$date_to)
						->get()->toArray();
				}else{
					$db_exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("
						SUM(P1) costeP1,
						SUM(P2) costeP2,
						SUM(P3) costeP3,
						SUM(P4) costeP4,
						SUM(P5) costeP5,
						SUM(P6) costeP6"
					))
						->where('date','>=',$date_from)
						->where('date','<=',$date_to)
						->get()->toArray();
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

				foreach ($aux_coste_activa[0] as $coste){
					$aux_index = 'costeP';

					$aux_impuesto = $aux_impuesto+(($coste + $coste_reactiva[$i][0][$aux_index.($index+1)] + $potencia_contratada[$i][0][$aux_index.($index+1)] + $aux_exceso_potencia[0][$aux_index.($index+1)])*0.0511269632)*$aux_iee;

					$index++;
				}

				if($cont['tipo'] == 1 OR $cont['tipo'] == 2){
					$iee[] = $aux_impuesto;
				}
				// if($i == 4)
				//     dd($coste_reactiva[$i][0]['costeP4'] , $potencia_contratada[$i][0]['costeP4'] , $exceso_potencia[$j][0]['costeP4'],$j);

				//    $impuesto[] = $aux_impuesto;

				$equipo[] = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
				// dd($equipo[0][0]->valor*($diasDiferencia+1));
				$i++;
				$j++;
			}elseif($cont['tipo'] == 3 && $cont['tarifa'] != 2 && $cont['tarifa'] != 3){

				$terminoVariable = $db->table('Coste_Termino_Variable')->select(\DB::raw("
					SUM(`Coste Termino Variable (€)`) valor
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->first();

				$terminoFijo = $db->table('Coste_Termino_Fijo')->select(\DB::raw("
					SUM(`Coste Termino Fijo (€)`) valor
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->first();

				$consumoGn = $db->table('Consumo_GN_kWh')->select(\DB::raw("
					SUM(`Consumo GN (kWh)`) consumo
				"))
					->where('date','>=',$date_from)
					->where('date','<=',$date_to)
					->first();

				$porcentajeIehc = $db->table('Impuesto_HC')->select(\DB::raw("
					Impuesto_HC valor
				"))
					->where('date_start','<=',$date_from)
					->where('date_end','>=',$date_to)
					->first();
				
				$precioAlquiler = $db->table('Equipo_de_medida')->select(\DB::raw("
					Alquiler_Equipo_Medida valor
				"))
					->where('date_start','<=',$date_from)
					->where('date_end','>=',$date_to)
					->first();	
					
				$terminoVariable = (float) $terminoVariable->valor;
				$terminoFijo		 = (float) $terminoFijo->valor;
				$consumoGn			 = (float) $consumoGn->consumo;
				$porcentajeIehc	 = (float) $porcentajeIehc->valor;
				$precioAlquiler	 = (float) $precioAlquiler->valor;

				$costeIehc = $consumoGn * $porcentajeIehc;
				$costeAlquiler = $precioAlquiler * ($diasDiferencia + 1);
				$costeIva = ($terminoVariable + $terminoFijo + $costeIehc + $costeAlquiler) * $porcentajeIva;
				$costeTotal = $costeIva * (1 + 1/$porcentajeIva);

				$contadorGas = [
					"nombre"		=> $cont["count_label"],
					"label"			=> "C".++$iterations,
					"domicilio" => $domicilio,
					// "markers" 	=> ["lat" => $domicilio->latitud, "lng" => $domicilio->longitud],
					"variable" 	=> (float) number_format($terminoVariable, 2, '.',','),
					"fijo"			=> (float) number_format($terminoFijo, 2, '.',','),
					"consumo"		=> (float) number_format($consumoGn, 2, '.',','),
					"iehc"			=> (float) number_format($costeIehc, 2, '.',','),
					"alquiler" 	=> (float) number_format($costeAlquiler, 2, '.',','),
					"iva" 			=> (float) number_format($costeIva, 2, '.',','),
					"total"			=> (float) number_format($costeTotal, 2, '.',',')
				];

				$totalContadoresGas["variable"] += $contadorGas["variable"];
				$totalContadoresGas["fijo"] 		+= $contadorGas["fijo"];
				$totalContadoresGas["consumo"] 	+= $contadorGas["consumo"];
				$totalContadoresGas["iehc"] 		+= $contadorGas["iehc"];
				$totalContadoresGas["alquiler"] += $contadorGas["alquiler"];
				$totalContadoresGas["iva"] 			+= $contadorGas["iva"];
				$totalContadoresGas["total"] 		+= $contadorGas["total"];

				$contadoresGas[] = $contadorGas;

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

				if($cont['tipo'] == 1 OR $cont['tipo'] == 2)
				{
					$iee[] = $aux_impuesto;
				}

				//  $impuesto[] = $aux_impuesto;
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

			// codigo nuevo para IEE

			if($cont['tipo'] == 1 OR $cont['tipo'] == 2){

				$sumatoria=0;
				if($cont['iee'] == 3){
					$aux_iee = 0;
				}elseif($cont['iee'] == 2){
					$aux_iee = 0.15;
				}else{
					$aux_iee = 1;
				}

				//  $sumatoria = $coste_activa + $coste_reactiva + $analisis_termino_potencia;
				$sumatoria = array_sum($aux_coste_activa[0]) + array_sum($aux_coste_reactiva[0]) + $analisis_termino_potencia;
				$impuesto_IEE = $sumatoria*0.0511269632*$aux_iee;
				array_push($impuesto,$impuesto_IEE);
				// fin de codigo nuevo para IEE

				// codigo nuevo para IVA
				$IVA_calculo = ($sumatoria + $impuesto_IEE + ($equipo[0][0]->valor*($diasDiferencia+1)))*0.21;
				array_push($IVA,$IVA_calculo);
				// fin de codigo nuevo para IVA

			}


			if(!isset($dir_image_count)){
				if(!is_null($db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first())){
					$dir_image_count = $db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first()->logo;
				} else {
					$dir_image_count =$db->table('Area_Cliente')->select(\DB::raw('`LOGOTIPO` logo'))->first();
				}
			}

			\DB::disconnect('mysql2');

			//aqui termina el for each
		}

		// codigo nuevo para el TOTAL


		if(!isset($dir_image_count)) {
			$dir_image_count = "";
		}

		// fin de codigo nuevo para el TOTAL

		$cont = $contador;
		$contador_count = count($contador_name);
		$aux_current_count = \DB::select("SELECT label_current_count FROM current_count WHERE user_id = ".$id);

		if(is_null($aux_current_count) || empty($aux_current_count)){
			\DB::insert("INSERT INTO current_count (label_current_count, user_id) VALUES ('".$current_count."',".$id.")");
		} else {
			\DB::update("UPDATE current_count SET label_current_count = '".$aux_current_count[0]->label_current_count."' WHERE user_id = ".$id);
		}

		$mapsKey = env("GOOGLE_MAP_API_KEY", "");
		$mapsURL = "https://maps.googleapis.com/maps/api/js?key=$mapsKey&callback=initialize";

		if(($id != 0 && Auth::user()->id == $id) || Auth::user()->tipo == 1){
			if($tipo_count < 3){
				return view('Dashboard.dashboard',compact('user','titulo','id','coste_activa', 'coste_reactiva', 'potencia_contratada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','label_intervalo','tipo_count','diasDiferencia','domicilio','dir_image_count','tipo_tarifa','iee','contador_name','contador_cups','contador_direccion','contador_tarifa','contador_count','total_termino_potencia','total_potencia_contratada','total_excesos_potencia','IVA'))
					->with('maps_url', $mapsURL)
					->with('markers', json_encode( $markers));
			} else {

				// dd(compact(
				// 	'user','id','ctrl',
				// 	'dir_image_count',
				// 	'label_intervalo','date_from','date_to',
				// 	'contadoresGas', 'totalContadoresGas',
				// 	'titulo','mapsURL'
				// ));

				return view('Gas.contadores', compact(
					'user', 'id', 'ctrl',
					'dir_image_count',
					'label_intervalo', 'date_from', 'date_to',
					'tipo_count', 'tipo_tarifa',
					'contadoresGas', 'totalContadoresGas',
					'titulo', 'mapsURL'
				))
					->with('maps_url', $mapsURL)
					->with('markers', json_encode( $markers ));
				// return view('Gas.contadores',compact(
				// 	'user','id','ctrl',
				// 	'domicilio','dir_image_count',
				// 	'hoy','label_intervalo','date_from','date_to','diasDiferencia',
				// 	'cont','tipo_count','array_contadores','tipo_tarifa','iee',
				// 	'termino_variable','termino_fijo','consumo_GN_kWh','I_E_HC','equipo_medida',
				// 	'titulo','mapsURL'
				// ))
				// 	->with('maps_url', $mapsURL)
				// 	->with('markers', json_encode( $markers ));
			}
		}
		return \Redirect::to('https://submeter.es/');
	}

	/**
	 * Return the latitude and location from the given address
	 *
	 * @param $address
	 * @return array
	 */
	private function getLatLong( $address )
	{
		try
		{
			$apiKey = env( "GOOGLE_MAP_API_KEY", "" );

			// google map geocode api url
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=$apiKey";

			// get the json response
			# $respJson = file_get_contents($url);

			$stream_opts = [
				"ssl" => [
					"verify_peer"       =>false,
					"verify_peer_name"  =>false,
				]
			];
			$respJson = file_get_contents(
				$url,
				false,
				stream_context_create( $stream_opts )
			);

			$output= json_decode($respJson);

			$lat = isset( $output->results[0]->geometry->location->lat ) ? $output->results[0]->geometry->location->lat : null;
			$lng = isset( $output->results[0]->geometry->location->lng ) ? $output->results[0]->geometry->location->lng : null;

			return [
				'lat' => $lat,
				'lng' => $lng,
			];
		}
		catch ( \Exception $exception )
		{
			Log::error(
				"ResumenContadoresController.getLatLong: Something went wrong getting the location for the given " .
				"address: $address. Description {$exception->getMessage()} "
			);

			return [
				'lat' => 0,
				'lng' => 0,
			];
		}
	}

}
