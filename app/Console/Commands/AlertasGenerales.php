<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AlertasGeneral;
use App\Mail\AlertaGeneral;
use Illuminate\Support\Facades\Mail;
use App\Count;
use App\User;
use App\EnergyMeter;
use Session;
use Auth;

class AlertasGenerales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas_generales:consumo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alertas generales';

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
    public function handle() {
        $alertas = AlertasGeneral::where('activado', TRUE)->get()->toArray();
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

		$dbList = $contadorPrev['production_databases'];
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
						[$conexion['field_name'], $str_condition, (double)$conexion['limit']], 
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

		$prev_filter_c = $db->table($conexion['table_name'])
		->select(\DB::raw("`date`, `time`, `".$conexion['field_name']."`"))
		->where([
			[$conexion['field_name'], $str_condition, (double)$conexion['limit']], 
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
				'control_avisos_fecha' => date('Y-m-d'), 
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
				sleep(3);
			}
		}
		\DB::disconnect('mysql2');
    }
}
