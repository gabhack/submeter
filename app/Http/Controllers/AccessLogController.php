<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\AccessLog;
use Session;
use Response;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Bootstrap\RegisterProviders;

class AccessLogController extends Controller
{
    protected function getAccessLogData($id){
        $user = User::find($id);
        $accesos = AccessLog::select(
            'user_email as email',
            'ip_address as ip',
            'access_status as estatus',
            'address_address as direccion',
            'address_latitude as latitud',
            'address_longitude as longitud',
            'local_access_date  as fecha_ingreso',
            'local_logout_date as fecha_salida'
        )
            ->where('user_email', $user->email)
            ->where('local_access_date', '>=', Carbon::now()->addMonths(-1))
            ->get();

        return $accesos;
    }

    protected function getUbicationsLogData($id){
        $user = User::find($id);
        $ubicaciones = AccessLog::select(DB::raw("distinct concat_ws(',',address_latitude, address_longitude) as coordenada"))
        ->where('user_email', $user->email)
        ->where('local_access_date', '>=', Carbon::now()->addMonths(-1))
        ->where('access_status', '=', 'ACCESS')
        ->orderBy('id', 'asc')
        ->get();

        return $ubicaciones;
    }

    protected function converUbicationGMapMarkers($ubicaciones){
        /* 
            $markers ='';
            foreach($ubicaciones as $ubicacion){
                $markers = $markers.'markers='.$ubicacion->coordenada.'&';
            }
            return $markers;
        */

        $markers = [];
        foreach ($ubicaciones as $ubicacion) {
            if ($ubicacion->coordenada === "") continue;
            list($lat, $lng) = explode(',', $ubicacion->coordenada);
            $markers[] = compact("lat", "lng");
        }
        return json_encode($markers);
    }

    protected function getCenterMap($ubicaciones){
        $center = '';
        foreach($ubicaciones as $ubicacion) {
                $center = 'center='.$ubicacion->coordenada.'&';
        }
        return $center;
    }

    public function getAccessLog($id)
    {
        $user = User::find($id);
        $contador = strtolower(request()->input('contador'));
        $titulo = "Control de Accesos";

        $interval = "";
        $flash_current_count = null;
        $session = Session::get('_flash');
        if (array_key_exists('intervalos', $session)) {
            $interval = $session['intervalos'];
            if (array_key_exists("current_count", $session)) {
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

        $accesos = $this->getAccessLogData($id);
        $ubicaciones = $this->getUbicationsLogData($id);
        $marcadores = $this->converUbicationGMapMarkers($ubicaciones);
        //$centerMap = $this->getCenterMap($ubicaciones);

        // return view('User.access_logs', compact('user', 'tipo_count', 'titulo', 'accesos', 'marcadores', 'centerMap'));
        return view('User.access_logs', compact('user', 'tipo_count', 'titulo', 'accesos', 'marcadores'));
    }

    public function getAccessLogCsv($id)
    {
        $accesos = $this->getAccessLogData($id);
        $filename = "LogdeAccesos.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Usuario','IP', 'Estatus','Direccion','Latitud', 'Longitud','Fecha Ingreso', 'Fecha Salida'), ';');
        foreach ($accesos as $registro) {
                    fputcsv($handle, array($registro->email,
                                           $registro->ip,
                                           $registro->estatus,
                                           $registro->direccion,
                                           $registro->latitud,
                                           $registro->longitud,
                                           $registro->fecha_ingreso,
                                           $registro->fecha_salida), ';');
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, $filename, $headers);
    }
}
