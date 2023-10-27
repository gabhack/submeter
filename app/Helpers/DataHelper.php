<?php

namespace App\Helpers;


use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use App\ProductionConfiguration;
use Session;
use App\ProductionField;
use App\ProductionFieldOperand;
use App\ProductionGroupField;
use App\ProductionGroupOperand;
use App\EnergyMeter;
use App\ProductionType;
use DateInterval;

class DataHelper
{

    public static function format_data($config)
    {
        $data = [
            'chart'=>[],
            'details'=> [],
            'csv' => [],
            'totals' => []
        ];
        if(!isset($config)) return $data;
        $current_dates = DataHelper::current_dates();
        if(!isset($config['fields'])) $config['fields'] = [];
        

        $db_data = DataHelper::extract_db_data($config['fields'],$config['meter_id'],$current_dates);
        
        $axes = DataHelper::getAxes($current_dates,$config);
        
        $db_collect = collect($db_data);
        
        $field_axes = [];
        $axe_index = 0;
        foreach ($axes as $axe_key=>$axe) 
        {
            $axe_index ++;
            /* Logica para obtener el valor */
            $f_row = $db_collect->where('g_datetime','>=',$axe['date_start'])->where('g_datetime','<',$axe['date_end']);
            //echo $axe_key . '<br>'; 
            //if($axe_key == 'last') dd($f_row);
            
            foreach ($config['fields'] as $field) 
            {
                $force_method = null;
                if(count($axes) - $axe_index <=1 && DataHelper::forceAvg($current_dates['interval'],$config))
                {
                    $force_method = 'avg';
                }
                
                $val = DataHelper::get_value($f_row,$field,$force_method);
                
                if(!array_key_exists($field['id'],$field_axes))
                {
                    $field['axes'] = [];
                    $field['total'] = 0;
                    $field_axes[$field['id']] = $field;
                }
                $field_axes[$field['id']]['axes'][$axe_key] = [
                    'label' => $axe['label'],
                    'label_extend' => $axe['label_extend'],
                    'value' => $val,
                    'value_formated'=> DataHelper::format_value($field,$val),
                    'value_number_formated'=> DataHelper::format_value($field,$val,true)
                ] ;
            }
        }
        
        //var_dump($field_axes[1]);
        //Calc totals
        foreach ($field_axes as $key=>$field) 
        { 
            $field_axes[$key]['total'] = DataHelper::total_value_extend($field,$db_collect) ;//DataHelper::total_value($field,$field['axes']);
            $field_axes[$key]['total_formated'] = DataHelper::format_value($field,$field_axes[$key]['total']);
        }
        
        //Data Chart Header
        $data['chart'] = array(
            'animationEnabled'=>false,
            'exportEnabled'=> true,
            'theme'=>'light2',
            'zoomEnabled'=>true,
            'culture' => 'es',
            'pdfHeight'=> '60',
            'title'=>array(
                'text'=>$config['name'],
                'fontSize'=> 18,
                'margin'=> 20,
                'fontColor'=> '#004165'
            ),
            
            //exportFileName: titulo+"-"+conta+"-"+date_from+"-"+date_to,
            'axisY'=>array(
                //'title'=> 'Bounce Rate',
                'lineColor'=> "#004165",
                'labelFontColor'=>  "#004165",
                'tickColor'=>  "#004165",
                'includeZero'=> true,
                'suffix'=> "",
                'margin' => 10
            ),
            "toolTip"=>array(
                "shared" => true
            ),
            'axisX'=> array(
                //'title'=> "Week of Year",
                //'prefix'=> "",
                'labelFontSize'=> 11,
                'titleFontSize'=> 12,
                'titleFontColor'=> "#004165",
                'lineColor'=> "#004165",
                'labelFontColor'=> "#004165",
                //interval: dataPlot[0].dataPoints.length > 24 ? 1 : 1, //# @Leo W* vamos asegurarnos de que cuando los intervaloes sean mas de 2 puntos no se encimen en el eje de las X
                'tickColor'=> "#004165",
                'includeZero'=> true,
                'interval'=> 1,
                'margin' => 10
            ),
            'legend'=>array(
                'cursor'=>'pointer',
                'markerMargin'=> 8,
            ),
            'data' => []
        );

        $data['details'] = [
            'header'=>[
                'interval' => $current_dates['leyend'],
                'fields' => []
            ],
            'rows' => [
                   
            ],
            'totals'=>[]
        ];

        $data['totals'] = [];
        $data['csv']['totals'] = [] ;//[$current_dates['leyend']];
        $data['csv']['details'] = [];
        
        
        foreach ($field_axes as $field) 
        {
            $chart_field = null;
            if(in_array(2, $field['destiny'])) //2 GRAFICAS
            {
                $chart_field = [
                    'type'=> $config['chart_type'],
                    'color'=> $config['chart_type'] != 'pie' ? $field['color'] : DataHelper::randomColor(),
                    'name'=> $field['display_name'],
                    //'markerType' => 'none',
                    'markerSize'=> 0,
                    'showInLegend'=> $config['chart_type'] != 'pie',
                    'toolTipContent' => '{name}: {y} ' . $field['unities'],
                    
                    'dataPoints' => []
                ];
            }
            
            $total_field = null;
            if(in_array(3, $field['destiny']))  //Totals
            {
                $total_field = [
                    'display_name' => $field['display_name'],
                    'color' => $field['color'],
                    'field_type' => $field['field_type'],
                    'field_type_name' => ProductionType::find($field['field_type'])->name,
                    'value' => $field['total_formated']
                ];
                
                $data['totals'][] = $total_field;
                //continue;
            }
            
            $detail_field = null;
            if(in_array(4, $field['destiny']))  //4 Details
            {
                $detail_field = [
                    'display_name'=>$field['display_name'],
                    'color'=>$field['color']
                ];
                
                $data['details']['header']['fields'][] = $detail_field;    
                $data['details']['totals'][] = $field['total_formated'];    
            }
            
            $field_csv = null;
            if(in_array(1, $field['destiny']))  //1 Csv
            {
                //$data['csv']['details'][$field_axe['field']['display_name']] = [];
                $field_csv = [
                    'display_name'=>$field['display_name'],
                    'color'=>$field['color']
                ];
                
                $data['csv']['totals'][] = [
                    'display_name' => $field['display_name'],
                    'color' => $field['color'],
                    'field_type' => $field['field_type'],
                    'field_type_name' => ProductionType::find($field['field_type'])->name,
                    'value' => $field['total_formated']
                ];
                
            }
            
            foreach ($field['axes'] as $key=>$axe) 
            {
                if(isset($chart_field))
                {
                    $yformat = '##,##0';
                    if($field['number_type'] == 1)
                    {
                        $d_part_arr = explode('.', $axe['value']);
                        if(count($d_part_arr) > 1) $d_part = strlen($d_part_arr[1]);
                        else $d_part = 0;

                        if($d_part > $field['decimals'])
                        {
                            $yformat = '##,##0.'. str_repeat('#',$field['decimals']);
                        }else
                        {
                            $yformat = '##,##0.'. str_repeat('#',$d_part) . str_repeat('0',$field['decimals'] - $d_part);
                        }
                    }


                    $chart_field['dataPoints'][] = [
                        'y'=>$axe['value'],
                        'color'=> $config['chart_type'] != 'pie' ? $field['color'] : DataHelper::randomColor() ,   
                        'yValueFormatString'=> $yformat,
                        'label'=>$axe['label']
                    ];
                }

                if(isset($detail_field))
                {
                    if(!array_key_exists($key,$data['details']['rows']))
                    {
                        $data['details']['rows'][$key] = [
                            'interval'=>(trim($axe['label'])  != '')? $axe['label']:$axe['label_extend'],
                            'fields'=>[],
                            'total'=>0
                        ];
                    }
                    $data['details']['rows'][$key]['fields'][] = $axe['value_number_formated'];
                }
                

                if(isset($field_csv))
                {
                    $data['csv']['details'][$key][$current_dates['leyend']] = (trim($axe['label'])  != '')? $axe['label']:$axe['label_extend'];
                    $data['csv']['details'][$key][$field['display_name']] = $axe['value_number_formated'];
                    
                }
                
            }
            if(isset($chart_field))
            {
                $data['chart']['data'][] = $chart_field;
            }
        }

        $details_rows = $data['details']['rows'];
        $data['details']['rows'] = [];
        foreach ($details_rows as $row) 
        {
            $data['details']['rows'][] = $row;
        }
        $csv_totals = $data['csv']['totals'];
        $csv_details = $data['csv']['details'];
        
        $data['csv']['totals'] = [];
        $r = 0;
        $totals1 = [];
        $totals2 = [];
        foreach ($csv_totals as $row) 
        {
            $data['csv']['totals'][0][$row['display_name']] = 'Tipo:'.$row['field_type_name'];
            $data['csv']['totals'][1][$row['display_name']] = '';
            $data['csv']['totals'][2][$row['display_name']] = $row['value'];
            
        }
        //$data['csv']['totals'] = [$totals1,$totals2];

        $data['csv']['details'] = [];
        foreach ($csv_details as $row) 
        {
            $data['csv']['details'][] = $row;
        }
        
        return $data;
    }

    private static function get_value($f_row,$field,$force_method = null)
    {
        $field_values = [];
        foreach ($field['database_fields'] as $d_field) 
        {
            $f_key = $d_field['connection'].'.'.$d_field['table'].'.'.$d_field['field'];
            if(isset($force_method))
            {
                $f_name = $force_method.'_value'; 
            }
            else
            {
                $f_name = $d_field['group_by'].'_value'; 
            }
             
            $value =DataHelper::$f_name($f_row,$f_key);     
            $field_values[$d_field['key']] = $value;
        }
        if(!array_key_exists('expression',$field)) return 0;
        $expresion = $field['expression'];
        
        foreach ($field_values as $key => $value) 
        {
            $expresion = str_replace($key,$value,$expresion);
        }
        
        $evaluated_expression = 0;
        try {
            eval('$evaluated_expression = '.$expresion.';');
        } catch (\Throwable $th) {
            $evaluated_expression = 0;
        }
        
        return $evaluated_expression;
    }

    private static function total_value($field,$list)
    {
        $values = collect($list)->transform(function($item,$key){
            return $item['value'];
        });
        switch ($field['operation_type']) {
            case '1': //Suma total
                return $values->sum();
            case '2': //Promedio
                return $values->avg();
            case '3': //Mediana
                return $values->median();
            case '4': //Min
                return $values->min();
            case '5': //Max
                return $values->max();
            case '6': //Desviacion estandar
                return DataHelper::stats_standard_deviation($values->all());
        }
        return 10;
    }

    private static function total_value_extend($field,$list)
    {
        $field_values = [];
        foreach ($field['database_fields'] as $d_field) 
        {
            $f_key = $d_field['connection'].'.'.$d_field['table'].'.'.$d_field['field'];
            $field_values[$d_field['key']] = DataHelper::total_value_db_field($f_key,$field['operation_type'],$list);
        }

        if(!array_key_exists('expression',$field)) return 0;
        $expresion = $field['expression'];
        
        foreach ($field_values as $key => $value) 
        {
            $expresion = str_replace($key,$value,$expresion);
        }
        
        $evaluated_expression = 0;
        try {
            eval('$evaluated_expression = '.$expresion.';');
        } catch (\Throwable $th) {
            $evaluated_expression = 0;
        }
        return $evaluated_expression;
    }

    private static function total_value_db_field($db_field_key,$operation,$list)
    {
        $values = [];
        foreach ($list as $key => $item) 
        {
            if(array_key_exists($db_field_key,$item))
            {
                $values[] = $item[$db_field_key];
            }
        }
        
        $values = collect($values);
        switch ($operation) {
            case '1': //Suma total
                return $values->sum();
            case '2': //Promedio
                return $values->avg();
            case '3': //Mediana
                return $values->median();
            case '4': //Min
                return $values->min();
            case '5': //Max
                return $values->max();
            case '6': //Desviacion estandar
                return DataHelper::stats_standard_deviation($values->all());
        }
        return 0;
    }

    private static function avg_value($f_row,$f_key)
    {
        $sum = 0;
        $count = 0;
        foreach ($f_row as $row) 
        {
            if(array_key_exists($f_key,$row))
            {
                $count++;
                $sum += $row[$f_key];
            }
        } 
        if($count == 0) return 0;
        return $sum/$count;
    }

    private static function max_value($f_row,$f_key)
    {
        $max = 0;
        foreach ($f_row as $row) 
        {
            if(array_key_exists($f_key,$row))
            {
                if($row[$f_key] > $max) $max = $row[$f_key];
            }
        } 
        return $max;
    }

    private static function min_value($f_row,$f_key)
    {
        $min = -1;
        
        foreach ($f_row as $row) 
        {
            if(array_key_exists($f_key,$row))
            {
                if($row[$f_key] < $min || $min == -1 ) $min = $row[$f_key];
            }
            
        } 
        return $min;
    }

    private static function rep_value($f_row,$f_key)
    {
        $sum = 0;
        foreach ($f_row as $row) 
        {
            if(array_key_exists($f_key,$row))
            {
                $sum = $sum  + $row[$f_key];
            }
        } 
        return $sum;
    }

    private static function extract_db_data($fields,$meter_id,$dates)
    {
        $data = [];
        $db_fields = [];
        
        foreach ($fields as $tfield) 
        {
            foreach ($tfield['database_fields'] as $dfield) 
            {
                $db_fields[$dfield['connection'].'.'.$dfield['table'].'.'.$dfield['field']] = $dfield;    
            }
        }
        
        $meter = EnergyMeter::find($meter_id);
        
        
        foreach ($db_fields as $t_field) 
        {
            if(empty($t_field['connection']) || empty($t_field['table']) ) continue;
            
            $conn = $meter->find_production_connection($t_field['connection']);
            
            if($conn != null)
            {
                \DB::purge('mysql2');
            
                config(['database.connections.mysql2.host' => $conn['host']]);
                config(['database.connections.mysql2.port' => $conn['port']]);
                config(['database.connections.mysql2.username' => $conn['username']]);
                config(['database.connections.mysql2.password' => $conn['password']]);
                env('MYSQL2_HOST',$conn['host']);
                env('MYSQL2_USERNAME', $conn['username']);
                env('MYSQL2_PASSWORD',$conn['password']);
                /*try {
                    \DB::connection('mysql2')->getPdo();
                } catch (\Exception $e) {
                    $data["error"] = true;
                    return $data;
                }*/
                
                $db = \DB::connection('mysql2');
                
                
                $date_from = $dates["date_from"];
                $date_to = $dates["date_to"];
                
                $table_data = $db->table(\DB::raw("`".$conn['database']."`.`".$t_field['table']."`"))
                                ->where("date", ">=", $date_from)
                                ->where("date", "<=", $date_to)
                                ->get()->toArray();
                
                foreach ($table_data as $row) 
                {
                    $t_row = (array) $row;
                    $data[$t_row['date'].'.t.'.$t_row['time']]['date'] = $t_row['date'];
                    $data[$t_row['date'].'.t.'.$t_row['time']]['time'] = $t_row['time'];
                    $data[$t_row['date'].'.t.'.$t_row['time']]['g_datetime'] = $t_row['date'] . ' '.$t_row['time'];
                    foreach ($t_row as $key => $value) 
                    {
                        $data[$t_row['date'].'.t.'.$t_row['time']][$conn['id'].".".$t_field['table'].".".$key] = $value;
                    }
                    
                }
                \DB::purge('mysql2');
            }
        }
        return $data;
    }

    
    public static function current_dates()
    {
        $flash = Session::get('_flash');
            $interval = "";            
            if(array_key_exists("date_from_personalice", $flash)){
                $date_from = $flash['date_from_personalice'];
                if(array_key_exists('intervalos', $flash))
                {
                    $interval = $flash['intervalos'];
                }
            }            
            
            if(!isset($date_from)){
                $dateInfo = DataHelper::getDatesAnalysis();
                $date_from = $dateInfo["date_from"];
                $date_to = $dateInfo["date_to"];
                $label_intervalo = $dateInfo["date_label"];
            } else {
                $flash = Session::get('_flash');
                
                $date_to = Session::get('_flash')['date_to_personalice'];
                if(array_key_exists("label_intervalo_navigation", $flash)){
                    $dateInfo = DataHelper::getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                } else {
                    $dateInfo = DataHelper::getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                }
            }
            
            $dates = [];
            $dates["date_from"] = $date_from;
            $dates["date_to"] = $date_to;

            $data_calculos = [];
            $data_calculos["date_from"] = $date_from;
            $data_calculos["date_to"] = $date_to;
            $data_calculos["interval"] = $interval;
            $data_calculos["leyend"] = $label_intervalo;
            return $data_calculos;
    }


    private static function getDatesAnalysis()
    {
        $interval = Session::get('_flash')['intervalos'];
        
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

    public static function getAxes($data_calculos,$config)
    {
        $output = [];
        $date_from = $data_calculos["date_from"];
        $date_to = $data_calculos["date_to"];
        $interval = $data_calculos["interval"];
        $maxDate = (new Carbon($date_to . ' 23:59:59'));
        $prevDate = null;
        $monthsNames = array(1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril",
            5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre",
            10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre");
        
        $daysNames = array("1"=>"Lunes", "2"=>"Martes", "3"=>"Miercoles",
            "4"=>"Jueves","5"=>"Viernes", "6"=>"Sabado", "7"=>"Domingo");
        
        $aux_label = "";
        $aux_interval = "";
        $interval_s = '';
        switch ($interval){
            case 1:
                $date_label = 'Ayer';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config['chart_interval_daily'].' minute', $date_to." 24:00:00");
                
                $interval_s = $config['chart_interval_daily'].' minutes';
                $c = 0;
                
                foreach ($period as $key => $date) {
                    if($date > $maxDate)
                    {
                        $interval_keys[] = 'last';//$maxDate->format("Y-m-d-H:i");//isset($prevDate) ? $prevDate : $date->format("Y-m-d-H:i")  ;
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }
                    
                    $c ++;
                    
                    if($c % (intval(count($period)/24))  == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = $date > $maxDate ? '24:00': $date->format("H:i");
                    }
                    
                    $prevDate = $date->format("Y-m-d-H:i");
                }
                
                $aux_label = "Hora: ";
                break;
            case 2:
                $date_label = 'Hoy';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config['chart_interval_daily'].' minute', $date_to." 24:00:00");
                $interval_s = $config['chart_interval_daily'].' minutes';
                $c = 0;
                foreach ($period as $key => $date) {
                    
                    if($date > $maxDate)
                    {
                        $interval_keys[] = 'last';//isset($prevDate) ? $prevDate : $date->format("Y-m-d-H:i")  ;
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }
                    
                    $c ++;
                    if($c % (count($period)/24) == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = $date > $maxDate ? '24:00': $date->format("H:i");
                    }
                    $prevDate = $date->format("Y-m-d-H:i");
                }
                
                $aux_label = "Hora: ";
                break;
            case 3:
                $date_label = 'Semana Actual';
                
                $period = new CarbonPeriod($date_from, $config['chart_interval_weekly'].' minute', $date_to);
                $interval_s = $config['chart_interval_weekly'].' minutes';
                $interval_values = [];
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($daysNames[$date->dayOfWeekIso],$interval_values))
                    {
                        $interval_values[] = $daysNames[$date->dayOfWeekIso];
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = $date->format("H:i");
                    }
                    
                }
                break;
            case 4:
                $date_label = 'Semana Anterior';
                
                $period = new CarbonPeriod($date_from, $config['chart_interval_weekly'].' minute', $date_to);
                $interval_s = $config['chart_interval_weekly'].' minute';
                $interval_values = [];
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($daysNames[$date->dayOfWeekIso],$interval_values))
                    {
                        $interval_values[] = $daysNames[$date->dayOfWeekIso];
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = $date->format("H:i");
                    }
                    
                }
                
                break;
            case 5:
                $date_label = 'Mes Actual';
                
                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                $interval_s = '1 days';
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    $interval_values[] = $date->day;
                    $interval_extend[] = $date->day;
                }
                $aux_label = "Día ";
                $aux_interval = 1;
                break;
            case 6:
                $date_label = 'Mes Anterior';
                
                $interval_keys = array();
                $interval_values = array();
                
                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                $interval_s = '1 days';
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    $interval_values[] = $date->day;
                    $interval_extend[] = $date->day;
                }
                $aux_label = "Día ";
                $aux_interval = 1;
                break;
            case 7:
                $date_label = 'Ultimo Trimestre';
                
                $interval_keys = array();
                $interval_values = array();
                
                $period = new CarbonPeriod($date_from, '7 day', $date_to);
                $interval_s = '7 days';
                
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($monthsNames[$date->month]."(".$date->year.")",$interval_values))
                    {
                        $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = '';
                    }
                    
                }
                
                /*$period = new CarbonPeriod($date_from, '1 month', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }*/
                
                break;
            case 10:
                $date_label = 'Trimestre Actual';
                
                $interval_keys = array();
                $interval_values = array();
                
                $period = new CarbonPeriod($date_from, '7 day', $date_to);
                $interval_s = '7 days';
                
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($monthsNames[$date->month]."(".$date->year.")",$interval_values))
                    {
                        $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = '';
                    }
                    
                }

                /*foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }*/
                
                break;
            case 8:
                $date_label = 'Último Año';
                
                $interval_keys = array();
                $interval_values = array();
                
                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                $interval_s = '1 month';
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                    $interval_extend[] = '';
                }
                
                break;
            case 11:
                $date_label = 'Año Actual';
                
                $interval_keys = array();
                $interval_values = array();
                
                
                $period = new CarbonPeriod($date_from, '1 month', $date_to);
                $interval_s = '1 month';
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                    $interval_extend[] = '';
                }
                
                break;
            case 9:
                $date_label = 'Personalizado';
                
                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                $interval_s = '1 days';
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    $interval_values[] = $date->format("Y-m-d");
                    $interval_extend[] = '';
                }
                
                break;
            default:
                $date_label = 'Hoy';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config['chart_interval_daily'].' minute', $date_to." 24:00:00");
                $interval_s = $config['chart_interval_daily'].' minute';
                $c = 0;
                foreach ($period as $key => $date) {
                    if($date > $maxDate)
                    {
                        $interval_keys[] = 'last';//isset($prevDate) ? $prevDate : $date->format("Y-m-d-H:i")  ;
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }

                    $c ++;
                    if($c % (count($period)/24) == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                        $interval_extend[] = '';
                    }else{
                        $interval_values[] = ' ';
                        $interval_extend[] = $date > $maxDate ? '24:00': $date->format("H:i");
                    }
                    $prevDate = $date->format("Y-m-d-H:i");
                }
                
                $aux_label = "Hora: ";
                break;
        }
        if ($interval_values[count($interval_values)-1] == '00:00' )$interval_values[count($interval_values)-1] = '24:00';
        $dateInterval = array();
        $dateInterval["interval_keys"] = $interval_keys;
        
        $dateInterval["interval_values"] = $interval_values;
        $dateInterval["aux_label"] = $aux_label;
        $dateInterval["interval"] = $aux_interval;
        //return $dateInterval;
        //var_dump($interval_keys);
        for ($i=0; $i < count($interval_keys) ; $i++) 
        { 
            if($interval_keys[$i] == 'last')
                $cd = Carbon::createFromFormat('Y-m-d-H:i', $interval_keys[$i-1]);//new Carbon($interval_keys[$i]);
            else
                $cd = Carbon::createFromFormat('Y-m-d-H:i', $interval_keys[$i]);//new Carbon($interval_keys[$i]);
            //var_dump($cd);
            //Carbon::createFromFormat('Y-m-d H-i', '2021-01-06-00:00')  
            $output[$interval_keys[$i]] = [
                'label' => $interval_values[$i],
                'label_extend' => $interval_extend[$i],
                'date_start' => $cd->toDateTimeString(),
                'date_end' => $cd->add(CarbonInterval::fromString($interval_s))->toDateTimeString()
            ];
        }
        //var_dump($output);
        return $output;
    }

    private static function stats_standard_deviation(array $a, $sample = false) {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }

    private static function format_value($field,$value,$hideUnities = false)
    {
        if($field['number_type'] == 2)
        {
            return number_format(intval($value),0,',','.') . ' ' . (!$hideUnities ? $field['unities']: '') ;
        } 
        else //Decimal
        {
            return number_format ($value,$field['decimals'],',','.') . ' ' . (!$hideUnities ? $field['unities']: '') ;
        }
    }

    private static function randomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        /*
        $r = floor(rand(0,1) * 255);
        $g = floor(rand(0,1) * 255);
        $b = floor(rand(0,1) * 255);
        return "rgb(" . $r . "," . $g . "," . $b . ")";
        */
    }

    public static function makeHostConnection($hostname,$port,$username,$password)
    {
        \DB::purge('mysql2');
            
        config(['database.connections.mysql2.host' => $hostname]);
        config(['database.connections.mysql2.port' => $port]);
        config(['database.connections.mysql2.username' => $username]);
        config(['database.connections.mysql2.password' => $password]);
        env('MYSQL2_HOST',$hostname);
        env('MYSQL2_USERNAME', $username);
        env('MYSQL2_PASSWORD',$password);
        
        \DB::connection('mysql2')->getPdo();
        
        $db = \DB::connection('mysql2');
        return $db;
    }

    public static function saveDayValues($handle,$database,$table,$field,$key,$value)
    {   
        $from =  Carbon::createFromFormat('Y-m-d', $key);
        $s_value = $value / 96;
        $from->hour(0);
        $from->minute(0);
        $from->second(0);
        
        $tq = '';
        for ($i=0; $i < 96; $i++) { 
            $tq = $tq . DataHelper::rawUpsert($handle,$database,$table,$field,$from->toDateString(),$from->toTimeString(),$s_value);
            $from->addMinutes(15);
        }
        $handle->unprepared($tq );
    }

    public static function rawUpsert($handle,$database,$table,$field,$date,$hour,$value)
    {
        $tupdate = "UPDATE `$database`.`$table` SET `$field`='$value' WHERE   DATE(`date`)= Date('$date') And `time`= '$hour';";
        $tinsert = "INSERT INTO `$database`.`$table` (`date`,`time`, `$field`) 
                    select  '$date','$hour','$value' where not exists(select 1 from `$database`.`$table` WHERE DATE(`date`)= Date('$date') And `time`= '$hour' );";
        
        /*
        $tupdate = "UPDATE `".$database."`.`".$table."` SET `".$field."`='$value' WHERE   DATE_FORMAT(`date`,'%Y-%m-%d %H:%i:%s')= '".$key."';";
        $tinsert = "INSERT INTO `".$database."`.`".$table."` (`date`, `".$field."`) 
                    select  '$key', '$value' where not exists(select 1 from `".$database."`.`".$table."` WHERE   DATE_FORMAT(`date`,'%Y-%m-%d %H:%i:%s')= '".$key."' );";
        
        */
        return $tinsert . $tupdate;
        
    }

    public static function runUpsert($handle,$database,$table,$field,$key,$value)
    {
        
        $tupdate = "UPDATE `".$database."`.`".$table."` SET `".$field."`='$value' WHERE   DATE_FORMAT(`date`,'%Y-%m-%d %H:%i:%s')= '".$key."';";
        $tinsert = "INSERT INTO `".$database."`.`".$table."` (`date`, `".$field."`) 
                    select  '$key', '$value' where not exists(select 1 from `".$database."`.`".$table."` WHERE   DATE_FORMAT(`date`,'%Y-%m-%d %H:%i:%s')= '".$key."' );";
        
        /*$tselect = "select 1 from  `".$database."`.`".$table."` WHERE   DATE_FORMAT(`date`,'%Y-%m-%d %H:%i')= '".$key."'";*/
        //echo $tinsert;
        $handle->unprepared($tinsert . $tupdate );
        //$handle->unprepared($tupdate );
        
        //$handle->update($tupdate, [$value]);

        /*$s = $handle->select($tselect);
        if(count($s) > 0)
            $handle->update($tupdate, [$value]);
        else
            $handle->insert($tinsert, [$key,$value]);*/
    }

    private static function forceAvg($interval,$config)
    {
        if($interval <= 2 || ($interval>=3 && $interval<=4 &&  $config['chart_interval_weekly'] <= 30))
            return true;
        return false;
    }

}