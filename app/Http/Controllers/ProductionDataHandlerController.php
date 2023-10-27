<?php

namespace App\Http\Controllers;

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
use Symfony\Component\VarDumper\VarDumper;

class ProductionDataHandlerController extends Controller
{
    private $max_depth = 30;
    private $fields_database;
    private $dict_fields_database;
    
    public function getFormatedProductionData($configuration_id)
    {
        $configuration = ProductionConfiguration::find($configuration_id);
        
        if($configuration)
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
            
            $dates = [];
            $dates["date_from"] = $date_from;
            $dates["date_to"] = $date_to;

            
            
            $data_calculos = [];
            $data_calculos["date_from"] = $date_from;
            $data_calculos["date_to"] = $date_to;
            $data_calculos["interval"] = $interval;
            
            $labels = $this->getLabelsPlot($data_calculos,$configuration);            
            
            $field_data = $this->constructFields($configuration);     
            
            $group_data = $this->constructGroupFields($configuration);
            
            $result_tables = $this->readDataTable($configuration, $dates);
            
            if($result_tables["error"])
            {
                $data = [];
                $data["error"] = true;
                return $data;
            }
            $table_data = $result_tables["table_data"];
            
            
            $date_table_data = [];
            
            ksort($table_data);
            /*$keys = [];
            foreach ($table_data as $row) 
            {
                foreach ($row as $key => $value) 
                {
                    $keys[$key] = 1;
                }
            }
            foreach ($table_data as $dt=>$row) 
            {
                foreach ($keys as $key => $value) 
                {
                    if(!array_key_exists($key,$row))
                    {
                        $table_data[$dt][$key] = null;
                    }
                }
            }*/
            
            //var_dump($table_data);
            foreach($table_data as $data_row)
            {
                $data_row = (array) $data_row;
                
                if(array_key_exists("date", $data_row) && array_key_exists("time", $data_row))
                {
                    $date = $data_row["date"];
                    $time = $data_row["time"];
                    
                    $key_plot = $this->getKeyPlot($interval, $date, $time,$labels["interval_keys"]);
                    
                    $values = $this->computeValuesRow($data_row, $field_data);
                    
                    if(!array_key_exists($key_plot, $date_table_data))
                    {
                        $date_table_data[$key_plot] = [];
                    }
                    
                    foreach($values as $key_val => $val)
                    {
                        if(!array_key_exists($key_val, $date_table_data[$key_plot]))
                        {
                            $date_table_data[$key_plot][$key_val] = [];
                        }
                        $date_table_data[$key_plot][$key_val][] = $val;
                    }
                    
                    foreach($data_row as $key_val => $val)
                    {
                        if(!array_key_exists($key_val, $date_table_data[$key_plot]))
                        {
                            $date_table_data[$key_plot][$key_val] = [];
                        }
                        $date_table_data[$key_plot][$key_val][] = $val;
                    }
                }
            
            }

            //To prepare operations
            foreach($field_data as $key => $field)
            {
                if($field['parser_format'] != '( _XXX_ )')
                {
                    
                    foreach ($date_table_data as &$dat) 
                    {
                        
                        $formated_expression = $field["expression"];
                        $i = 0;
                        
                        foreach($field['operands'] as $operand)
                        {
                            $f = $operand['content'];
                            $avg = 0;
                            
                            if($operand['type'] != 3)
                            {
                                if(array_key_exists($f,$dat))
                                {
                                    foreach ($dat[$f] as $v) 
                                    {
                                        $avg += $v;
                                    } 
                                    if(count($dat[$f]) > 0) $avg = $avg/count($dat[$f]);
                                }
                            
                                $data_col = sprintf("%.15e", $avg);
                                
                                $index_database = str_replace(' ','',$operand['expression']);
                                $index_database = str_replace('(','',$index_database);
                                $index_database = str_replace(')','',$index_database);
                                
                                //var_dump($index_database);
                                $formated_expression = str_replace($index_database, $data_col, $formated_expression);
                                //var_dump($data_col);
                                //var_dump($formated_expression);
                            }
                            $i++;
                        }
                        
                        try 
                        {
                            eval('$evaluated_expression = '.$formated_expression.';');                
                        }
                        catch(\ParseError $e)
                        {
                            $evaluated_expression = null;
                        }
                        //var_dump($evaluated_expression);
                        //break;
                        /*$operandsAvg = [];
                        foreach($field['operands'] as $operand)
                        {

                        }*/
                        //var_dump($dat);
                        // break;
                        $dat[$key] = [$evaluated_expression];
                    }
                }
            }
            //var_dump($date_table_data);
            /*
            var_dump($date_table_data);
            var_dump($date_table_data['2020-10-03-00:00']['c1']);
            var_dump($date_table_data['2020-10-03-00:00']['c2']);
            var_dump($date_table_data['2020-10-03-00:00']['resta']);
            */

            $label_keys = $labels["interval_keys"];
            
            $group_date_table = [];
            $group_totals_table = [];
            
            foreach($label_keys as $lkey)
            {
                if(!array_key_exists($lkey, $group_date_table))
                {
                    $group_date_table[$lkey] = [];
                }
                
                foreach($group_data as $keyg => $gdata)
                {
                    if(!array_key_exists($lkey, $group_date_table[$lkey]))
                    {
                        $group_date_table[$lkey][$keyg] = 0;
                    }
                    
                    if(!array_key_exists($keyg, $group_totals_table))
                    {
                        $group_totals_table[$keyg] = [];
                    }
                    
                    if(array_key_exists($lkey, $date_table_data) && is_array($date_table_data[$lkey]))
                    {
                        $group_date_table[$lkey][$keyg] = $this->computeValuesGroup($gdata, $date_table_data[$lkey]);
                        //var_dump($gdata['operands']);
                        //var_dump($group_date_table[$lkey][$keyg]);*/
                    }
                    $group_totals_table[$keyg][] = $group_date_table[$lkey][$keyg];
                
                }
            
            }
            
            
            
            $group_totals = [];
            
            foreach($group_totals_table as $gkey => $gdata)
            {
                $value = $this->computeValuesTotalGroup($group_data, $gdata, $gkey);
                $group_totals[$gkey] = $value;
            }
            
            $production_data = [];
            $production_data["group_totals"] = $group_totals;
            $production_data["group_totals_table"] = $group_totals_table;
            $production_data["group_data"] = $group_data;
            $production_data["group_date_table"] = $group_date_table;
            $production_data["labels"] = $labels;
            
            $data = [];
            $data["error"] = false;
            $data["production"] = $production_data;  
            
            return $data;
        }
            
        $data = [];
        $data["error"] = true;
        return $data;
    }
    
    private function computeValuesRow($data_row, &$field_data)
    {
        $computed_values = [];
        
        foreach($field_data AS $key => $config)
        {
            $expression = $config["expression"];

            $formated_expression = $expression;
            for($i = 0; $i < count($this->fields_database); $i++)
            {
                $key_database = $this->fields_database[$i];
                
                $index_database = "#".$i;
                if(array_key_exists($key_database, $data_row))
                {
                    $data_col = sprintf("%.15e", $data_row[$key_database]);
                }
                else
                {
                    $data_col = 0;
                }
                $formated_expression = str_replace($index_database, $data_col, $formated_expression);
            }
            
            try 
            {
                eval('$evaluated_expression = '.$formated_expression.';');                
            }
            catch(\ParseError $e)
            {
                $evaluated_expression = null;
            }
            catch(\ErrorException $e)
            {
                $evaluated_expression = null;
            }
            if($evaluated_expression != null) $computed_values[$key] = $evaluated_expression;
        }
        return $computed_values;
    }
    
    private function computeValuesGroup($group_data, $data)
    {
        $parser = $group_data["parser_format"];
        if(count($group_data["operands"]) == 0)
        {
            return 0;
        }
        $operand = $group_data["operands"][0];
        /*if($operand["type"] == 2)
        {
            return (float)$data[];//(float)$operand["content"];
        }*/
        if(!array_key_exists($operand["content"], $data))
        {
            return 0;
        }
        $value = 0;
        $dop = $data[$operand["content"]];
        $count_dop = count($dop);        
        switch($parser)
        {
            case "SUM":
                $value = array_sum($dop);                
            break;
            case "AVG":                
                if($count_dop > 0)
                {
                    $value = array_sum($dop) / $count_dop;
                }
            break;
            case "MIN":
                if($count_dop > 0)
                {
                    $value = min($dop);
                }
            break;
            case "MAX":
                if($count_dop > 0)
                {
                    $value = max($dop);
                }
            break;
            case "STDEV":
                if($count_dop > 0)
                {
                    $value = sqrt($this->getVariance($dop));
                }
            break;
            case "FIRST":
                if($count_dop > 0)
                {
                    $value = $dop[0];
                }
            break;
            case "LAST":
                if($count_dop > 0)
                {
                    $value = $dop[$count_dop - 1];
                }
            break;
            case "COUNT":
                if($count_dop > 0)
                {
                    $value = $count_dop;
                }
            break;
        }
        return $value;
    }
    
    private function computeValuesTotalGroup($group_data, $data, $data_key)
    {
       
        if(!array_key_exists($data_key, $group_data))
        {
            return 0;
        }
        $parser = $group_data[$data_key]["parser_format"];
        $value = 0;
        $dop = $data;
        $count_dop = count($dop);
        switch($parser)
        {
            case "SUM":
                $value = array_sum($dop);
                break;
            case "AVG":
                if($count_dop > 0)
                {
                    $value = array_sum($dop) / $count_dop;
                }
                break;
            case "MIN":
                if($count_dop > 0)
                {
                    $value = min($dop);
                }
                break;
            case "MAX":
                if($count_dop > 0)
                {
                    $value = max($dop);
                }
                break;
            case "STDEV":
                if($count_dop > 0)
                {
                    $value = sqrt($this->getVariance($dop));
                }
                break;
            case "FIRST":
                if($count_dop > 0)
                {
                    $value = $dop[0];
                }
                break;
            case "LAST":
                if($count_dop > 0)
                {
                    $value = $dop[$count_dop - 1];
                }
                break;
            case "COUNT":
                if($count_dop > 0)
                {
                    $value = $count_dop;
                }
                break;
        }
        return $value;
    }
    
    private function getVariance(array $data)
    {
        $variance = 0.0;
        $totalElementsInArray = count($data);
        // Calc Mean.
        $averageValue = array_sum($data) / $totalElementsInArray;
        
        foreach ($data as $item) {
            $variance += pow(abs($item - $averageValue), 2);
        }
        
        return $variance;
    }
    
    private function readDataTable($configuration, $dates)
    {
        //Leo W Este metodo se cambia para traer ahora un array de tablas
        $data = [];
        $data["error"] = false;
        $data["table_data"] = [];

        $tables = \DB::select('
            SELECT field_database,field_table FROM production_field_operands WHERE EXISTS(SELECT 1 FROM production_fields WHERE id = production_field_operands.production_field_id AND configuration_id = '.$configuration->id.' ) GROUP BY field_database,field_table 
            UNION 
            SELECT field_database,field_table FROM production_group_operands WHERE EXISTS(SELECT 1 FROM production_group_fields WHERE id = production_group_operands.production_group_field_id AND configuration_id = '.$configuration->id.')  GROUP BY field_database,field_table
        ');

        $meter = EnergyMeter::find($configuration->meter_id);
        
        $final_result = [];
        foreach ($tables as $t_info) 
        {
            if(empty($t_info->field_database) || empty($t_info->field_table) ) continue;
            
            $conn = $meter->find_production_connection($t_info->field_database);
            
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
                try {
                    \DB::connection('mysql2')->getPdo();
                } catch (\Exception $e) {
                    $data["error"] = true;
                    return $data;
                }
                
                $db = \DB::connection('mysql2');
                
                $date_from = $dates["date_from"];
                $date_to = $dates["date_to"];
                
                $table_data = $db->table(\DB::raw("`".$conn['database']."`.`".$t_info->field_table."`"))
                                ->where("date", ">=", $date_from)
                                ->where("date", "<=", $date_to)
                                ->get()->toArray();
                foreach ($table_data as $row) 
                {
                    $t_row = (array) $row;
                    $final_result[$t_row['date'].'.t.'.$t_row['time']]['date'] = $t_row['date'];
                    $final_result[$t_row['date'].'.t.'.$t_row['time']]['time'] = $t_row['time'];
                    $final_result[$t_row['date'].'.t.'.$t_row['time']]['g_datetime'] = $t_row['date'] . '-'.$t_row['time'];
                    foreach ($t_row as $key => $value) 
                    {
                        $final_result[$t_row['date'].'.t.'.$t_row['time']][$conn['id'].".".$t_info->field_table.".".$key] = $value;
                    }
                    
                }
                \DB::purge('mysql2');
            }
            //$data["table_data"][] = $table_data;
        }
        $data["table_data"] = $final_result;
        
        return $data;
    }
    
    public function getDatesAnalysis()
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
    
    private function constructGroupFields($configuration)
    {
        $fields = ProductionGroupField::where("configuration_id", $configuration->id)->get();
        $field_data = [];
        
        foreach($fields as $field)
        {
            $field_data[$field->name] = [
                "operands" => [],
                "name" => $field->name,
                "type" => $field->production_type->name,
                "display_name" => $field->display_name,
                "parser_format" => $field->operation->parser_format,
                "show_type" => $field->show_type_id,
                "number_type" => $field->number_type_id,
                "color" => $field->color,
                "units" => $field->units,
                "decimals" => $field->decimal_count
            ];
            
            $operands = ProductionGroupOperand::where("production_group_field_id", $field->id)->get();
            foreach ($operands as $operand)
            {
                $content = $operand->field_content;
                if($operand->field_type_id == 2)
                {
                    $content = $operand->field_database .'.'. $operand->field_table . '.' .$operand->field_content;
                }
                $operand_data = [
                    "type" => $operand->field_type_id,
                    "content" => $content,
                    "database" => $operand->field_database,
                    "table" => $operand->field_table
                ];
                $field_data[$field->name]["operands"][] = $operand_data;
            }
        }
        
        return $field_data;
    }
    
    private function constructFields($configuration)
    {
        $fields = ProductionField::where("configuration_id", $configuration->id)->get();
        $field_data = [];
        
        foreach($fields as $field)
        {
            $field_data[$field->name] = [
                "operands" => [],
                "computed" => false,
                "expression" => "",
                "parser_format" => $field->operation->parser_format,
                "min_operands" => $field->operation->min_operands,
                "max_operands" => $field->operation->max_operands
            ];
            
            $operands = ProductionFieldOperand::where("production_field_id", $field->id)->get();
            foreach ($operands as $operand)
            {
                $operand_data = [
                    "type" => $operand->field_type_id,
                    "content" => $operand->field_content,
                    "database" => $operand->field_database,
                    "table" => $operand->field_table,
                    "expression" => ""
                ];
                $field_data[$field->name]["operands"][] = $operand_data; 
            }
        }
        
        $this->fields_database = [];
        $this->dict_fields_database = [];
        foreach($field_data as &$field)
        {
            if(!$field["computed"])
            {
                $expression = $this->computeFieldData($field_data, $field, 1);
                $field["computed"] = true;
                $field["expression"] = $expression;
            }
        }
        
        return $field_data;
    }
    
    private function computeFieldData(&$field_data, &$field, $depth)
    {
        if($field["computed"]) 
        {
            return "";
        }
        
        if($depth > $this->max_depth) 
        {
            return "";
        }
        
        $expr_operand = [];
        foreach($field["operands"] as &$operand)
        {
            if($operand["type"] == 3) 
            {
                $operand["expression"] = sprintf("%.15e", $operand["content"]);                
            }
            else if($operand["type"] == 2)
            {
                if(!array_key_exists($operand["content"], $this->dict_fields_database))
                {
                    $this->fields_database[] = $operand["database"] .'.'. $operand["table"] . '.' .$operand["content"];
                    $this->dict_fields_database[$operand["database"] .'.'. $operand["table"] . '.' .$operand["content"]] = count($this->fields_database) - 1;
                }
                $operand["expression"] = "#".$this->dict_fields_database[$operand["database"] .'.'. $operand["table"] . '.' .$operand["content"]];
            }
            else if($operand["type"] == 1)
            {
                if(array_key_exists($operand["content"], $field_data))
                {
                    $f_operand = $field_data[$operand["content"]];
                    if($f_operand["computed"])
                    {
                        $operand["expression"] = $f_operand["expression"];
                    }
                    else
                    {
                        $operand["expression"] = $this->computeFieldData($field_data, $f_operand, $depth + 1);
                    }
                }
                else
                {
                    $operand["expression"] = 0;
                }
            }
            $expr_operand[] = $operand["expression"];
        }
        
        $field_expr = "";
        if($field["max_operands"] == $field["min_operands"])
        {
            $field_expr = str_replace("_XXX_", implode(" ", $expr_operand), $field["parser_format"]);
        }
        else
        {
            $field_expr = implode(" ". $field["parser_format"]. " ", $expr_operand);
        }
        
        $field["computed"] = true;
        $field["expression"] = $field_expr;
        
        return $field_expr;
    }
    
    private function getLabelsPlot($data_calculos,$config)
    {
        $date_from = $data_calculos["date_from"];
        $date_to = $data_calculos["date_to"];
        $interval = $data_calculos["interval"];
        $maxDate = (new Carbon($date_to . ' 23:00'));
        
        $monthsNames = array(1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril",
            5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre",
            10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre");
        
        $daysNames = array("1"=>"Lunes", "2"=>"Martes", "3"=>"Miercoles",
            "4"=>"Jueves","5"=>"Viernes", "6"=>"Sabado", "7"=>"Domingo");
        
        $aux_label = "";
        $aux_interval = "";
        switch ($interval){
            case 1:
                $date_label = 'Ayer';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config->chart_interval_daily.' minute', $date_to." 24:00:00");
                $c = 0;
                foreach ($period as $key => $date) {
                    if($date > $maxDate)
                    {
                        $interval_keys[] = $maxDate->format("Y-m-d-H:i");
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }
                    
                    $c ++;
                    if($c % (count($period)/24) == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                    }else{
                        $interval_values[] = ' ';
                    }
                }
                $aux_label = "Hora: ";
                break;
            case 2:
                $date_label = 'Hoy';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config->chart_interval_daily.' minute', $date_to." 24:00:00");
                $c = 0;
                foreach ($period as $key => $date) {
                    if($date > $maxDate)
                    {
                        $interval_keys[] = $maxDate->format("Y-m-d-H:i");
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }

                    $c ++;
                    if($c % (count($period)/24) == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                    }else{
                        $interval_values[] = ' ';
                    }
                }
                
                $aux_label = "Hora: ";
                break;
            case 3:
                $date_label = 'Semana Actual';
                
                $period = new CarbonPeriod($date_from, $config->chart_interval_weekly.' minute', $date_to);
                $interval_values = [];
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($daysNames[$date->dayOfWeekIso],$interval_values))
                    {
                        $interval_values[] = $daysNames[$date->dayOfWeekIso];
                    }else{
                        $interval_values[] = ' ';
                    }
                    
                }
                break;
            case 4:
                $date_label = 'Semana Anterior';
                
                $period = new CarbonPeriod($date_from, $config->chart_interval_weekly.' minute', $date_to);
                $interval_values = [];
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d-H:i");
                    if(!in_array($daysNames[$date->dayOfWeekIso],$interval_values))
                    {
                        $interval_values[] = $daysNames[$date->dayOfWeekIso];
                    }else{
                        $interval_values[] = ' ';
                    }
                    
                }
                
                break;
            case 5:
                $date_label = 'Mes Actual';
                
                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $date->day;
                }
                $aux_label = "Día ";
                $aux_interval = 1;
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
                $aux_label = "Día ";
                $aux_interval = 1;
                break;
            case 7:
                $date_label = 'Ultimo Trimestre';
                
                $interval_keys = array();
                $interval_values = array();
                
                $period = new CarbonPeriod($date_from, '7 day', $date_to);
                
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    if(!in_array($monthsNames[$date->month]."(".$date->year.")",$interval_values))
                    {
                        $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                    }else{
                        $interval_values[] = ' ';
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
                
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    if(!in_array($monthsNames[$date->month]."(".$date->year.")",$interval_values))
                    {
                        $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                    }else{
                        $interval_values[] = ' ';
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
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m");
                    $interval_values[] = $monthsNames[$date->month]."(".$date->year.")";
                }
                
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
                
                break;
            case 9:
                $date_label = 'Personalizado';
                
                $period = new CarbonPeriod($date_from, '1 days', $date_to);
                foreach ($period as $key => $date) {
                    $interval_keys[] = $date->format("Y-m-d");
                    $interval_values[] = $date->format("Y-m-d");
                }
                
                break;
            default:
                $date_label = 'Hoy';
                
                $period = new CarbonPeriod($date_from." 00:00:00", $config->chart_interval_daily.' minute', $date_to." 24:00:00");
                $c = 0;
                foreach ($period as $key => $date) {
                    if($date > $maxDate)
                    {
                        $interval_keys[] = $maxDate->format("Y-m-d-H:i");
                    } else{
                        $interval_keys[] = $date->format("Y-m-d-H:i");
                    }

                    $c ++;
                    if($c % (count($period)/24) == 0) //@Leo W* para hacer configurable los intervalos que se muestran en la grafica
                    {
                        $interval_values[] = ($date->hour > 9 ? $date->hour : '0' . $date->hour  ).":". ($date->minute > 9 ? $date->minute : '0' . $date->minute );
                    }else{
                        $interval_values[] = ' ';
                    }
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
        return $dateInterval;
    }
    
    private function getKeyPlot($interval, $date, $time,$keys)
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s", $date." ".$time);
        $key = $date->format("Y-m-d-H:i:s");
        $l = '';
        foreach ($keys as $k) 
        {
            if($key <= $k)
            {
                break;
            }
            $l = $k;
        }
        return $l;
        /*var_dump($key);
        var_dump($keys);
        $key = "";
        switch ($interval){
            case 1:
                $date_label = 'Ayer';
                $key = $date->format("Y-m-d-H:i");
                break;
            case 2:
                $date_label = 'Hoy';
                $key = $date->format("Y-m-d-H:i");
                break;
            case 3:
                $date_label = 'Semana Actual';
                $key = $date->format("Y-m-d-H:i");
                break;
            case 4:
                $date_label = 'Semana Anterior';
                $key = $date->format("Y-m-d-H:i");
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
                $key = $date->format("Y-m-d");
                break;
            case 10:
                $date_label = 'Trimestre Actual';
                $key = $date->format("Y-m-d");
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
        
        return $key;*/
    }

    private function computeValuesRowAvgs($data_row, &$field_data)
    {
        $computed_values = [];
        
        foreach($field_data AS $key => $config)
        {
            $expression = $config["expression"];
            
            $formated_expression = $expression;
            for($i = 0; $i < count($this->fields_database); $i++)
            {
                $key_database = $this->fields_database[$i];
                
                $index_database = "#".$i;
                if(array_key_exists($key_database, $data_row))
                {
                    $data_col = sprintf("%.15e", $data_row[$key_database]);
                }
                else
                {
                    $data_col = 0;
                }
                $formated_expression = str_replace($index_database, $data_col, $formated_expression);
            }
            
            try 
            {
                eval('$evaluated_expression = '.$formated_expression.';');                
            }
            catch(\ParseError $e)
            {
                $evaluated_expression = null;
            }
            catch(\ErrorException $e)
            {
                $evaluated_expression = null;
            }
            if($evaluated_expression != null) $computed_values[$key] = $evaluated_expression;
        }
        return $computed_values;
    }
}
