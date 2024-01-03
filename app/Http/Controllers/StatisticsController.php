<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Indicator;
use App\User;
use App\CurrentCount;
use App\EnergyMeter;
use App\Enterprise;
use App\IndicatorConfiguration;
use Validator;
use Auth;
use Session;
use Exception;
use Log;

use App\EnterpriseUser;
use App\StatisticConfiguration;
use App\StatisticConfigurationsRepresentation;

class StatisticsController extends Controller
{

    public function listAll(Request $request, $user_id = 0)
    {
        $contador = StatisticsController::currentCounter();
        return view(
            'statistics.config.all',
            array(
                'titulo' => 'Administración General',
                'user' => $user_id != 0 ? User::find($user_id) : Auth::user(),
                'tipo_count' => null,
                'contador2' => $contador,
                'tipo_count' => $contador->tipo
            )
        );
    }

    public function list(Request $request, $type, $user_id = 0)
    {
        $session_user_id = Auth::user()->id;
        if ($user_id != $session_user_id) {
            $path_redirect = "/estadisticas/configuracion/" . $type . "/" . $session_user_id;
            return redirect($path_redirect);
        }
        $contador = StatisticsController::currentCounter();
        return view(
            'statistics.config.list',
            array(
                'titulo' => 'Administración General',
                'user' => ($user_id != 0 && $user_id == $session_user_id) ? User::find($user_id) : Auth::user(),
                'tipo_count' => null,
                'type' => $type,
                'contador2' => $contador,
                'tipo_count' => $contador->tipo
            )
        );
    }

    public function insert(Request $request, $type)
    {
        $contador = StatisticsController::currentCounter();
        return view(
            'statistics.config.insert',
            array(
                'titulo' => 'Administración General',
                'user' => Auth::user(),
                'tipo_count' => null,
                'type' => $type,
                'contador2' => $contador,
                'tipo_count' => $contador->tipo
            )
        );
    }

    public function update(Request $request, $type = '', $id = 0)
    {
        $session_user_id = Auth::user()->id;
        if ($id == 0) {
            if (in_array($type, ['produccion', 'indicadores', 'representacion'])) {
                $path_redirect = "/estadisticas/configuracion/" . $type . "/" . $session_user_id;
                return redirect($path_redirect);
            } else {
                $path_redirect = "/resumen_energia_potencia/" . $session_user_id;
                return redirect($path_redirect);
            }
        } else if (!in_array($type, ['produccion', 'indicadores', 'representacion'])) {
            $path_redirect = "/resumen_energia_potencia/" . $session_user_id;
            return redirect($path_redirect);
        }

        $contador = StatisticsController::currentCounter();
        return view(
            'statistics.config.update',
            array(
                'titulo' => 'Administración General',
                'user' => Auth::user(),
                'tipo_count' => null,
                'type' => $type,
                'id' => $id,
                'contador2' => $contador,
                'tipo_count' => $contador->tipo
            )
        );
    }

    public function resume(Request $request, $type = '', $user_id = 0)
    {
        try {
            $session_user_id = Auth::user()->id;

            if ($type == '' || !in_array($type, ['produccion', 'indicadores', 'representacion'])) {
                $path_redirect = "/resumen_energia_potencia/" . $session_user_id;
                return redirect($path_redirect);
            }

            if ($user_id != $session_user_id) {
                $current_url = url()->current();
                $urlType = "produccion";

                if (strpos($current_url, "indicadores") !== false) {
                    $urlType = "indicadores";
                } elseif (strpos($current_url, "representacion") !== false) {
                    $urlType = "representacion";
                }

                $path_redirect = '/estadisticas/' . $urlType . '/' . $session_user_id;

                return redirect($path_redirect);
            }

            $user = User::find($user_id);

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
            $dataRequest["interval"] = $interval;
            $dataRequest["flash_current_count"] = $flash_current_count;

            $contador = ContadorController::getCurrrentController($dataRequest);

            $interval = "";
            $flash_current_count = null;
            $session = $request->session()->get('_flash');
            if (array_key_exists('intervalos', $session)) {
                $interval = $session['intervalos'];
                if (array_key_exists("current_count", $session)) {
                    $flash_current_count = $session['current_count'];
                }
            }

            $flash = Session::get('_flash');
            $dataHandler = new ProductionDataHandlerController();
            if (array_key_exists("date_from_personalice", $flash)) {
                $date_from = $flash['date_from_personalice'];
            }

            if (!isset($date_from)) {
                $dateInfo = $dataHandler->getDatesAnalysis();
                $date_from = $dateInfo["date_from"];
                $date_to = $dateInfo["date_to"];
                $label_intervalo = $dateInfo["date_label"];
            } else {
                $flash = Session::get('_flash');

                $date_to = Session::get('_flash')['date_to_personalice'];
                if (array_key_exists("label_intervalo_navigation", $flash)) {
                    $dateInfo = $dataHandler->getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                } else {
                    $dateInfo = $dataHandler->getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                }
            }

            if ($type == 'indicadores') {
                $title = 'Indicadores Energéticos';
            } elseif ($type == 'representacion') {
                $title = 'Representación Datos';
            } else {
                $title = 'Producción Submetering';
            }

            $userEnterprice = EnterpriseUser::where("user_id", $user->id)->first();
            if ($type == 'representacion') {
                $configs = StatisticConfigurationsRepresentation::where("enterprise_id", $userEnterprice->enterprise_id)
                    ->where('meter_id', $contador->id)
                    ->where('users_id', $user->id)
                    ->orderBy('Order_Orden', 'asc')
                    ->get();
            } else {
                $configs = StatisticConfiguration::where("enterprise_id", $userEnterprice->enterprise_id)
                    ->where('type', $type)
                    ->where('meter_id', $contador->id)
                    ->get();
            }
            $configs = $this->sortConfigsByOrderField($configs);
            return view(
                'statistics.resume',
                array(
                    'user' => $user,
                    //Auth::user(),
                    'titulo' => $title,
                    'label_intervalo' => $label_intervalo,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'configurations' => $configs,
                    'contador2' => $contador,
                    'type' => $type,
                    'tipo_count' => $contador->tipo,
                    'template' => `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>`
                )
            );

        } catch (\Exception $e) {
            Log::error('Error en el método resume: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Se ha producido un error interno'], 500);
        }
    }

    public function sortConfigsByOrderField($configs)
    {
        return $configs->sortBy(function ($config) {
            return $config->Order_orden === null ? PHP_INT_MAX : $config->Order_orden;
        });
    }

    public function manual(Request $request, $user_id)
    {
        $contador = StatisticsController::currentCounter();
        return view(
            'statistics.manual',
            array(
                'titulo' => 'Carga de datos manuales',
                'user' => User::find($user_id),
                //Auth::user(),
                'tipo_count' => null,
                'contador2' => $contador,
                'tipo_count' => $contador->tipo
            )
        );
    }

    private static function currentCounter()
    {
        $user = Auth::user();

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
        $dataRequest["interval"] = $interval;
        $dataRequest["flash_current_count"] = $flash_current_count;

        $contador = ContadorController::getCurrrentController($dataRequest);
        return $contador;
    }
}
