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

use App\EnterpriseUser;
use App\Helpers\DataHelper;
use App\ManualConfiguration;
use App\ProductionConfiguration;
use App\ProductionField;
use App\ProductionGroupField;
use App\ProductionType;
use App\StatisticConfiguration;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;

class StatisticsApiController extends Controller
{

    /** Api global Functions */
    public function enterprice_list(Request $request)
    {
        return response(Enterprise::byUser()->get());
    }

    public function enterprice_meters_list(Request $request,$id)
    {
        $response = [];
        $e = Enterprise::find($id);
        if($e == null) return $response;
        foreach ($e->enterprise_meters as $c) 
        {
            $counter = EnergyMeter::find($c->meter_id);
            if($counter != null)
            {
                $response[] = [
                    'id' => $counter->id,
                    'name' => $counter->count_label
                ];
            }
        }
        return $response;
    }

    public function production_types_list(Request $request)
    {
        return response(ProductionType::all());
    }

    /** End Api Functions */
    
    public function list(Request $request)
    {
        /*
            if($request->has('emp_id'))
            return response(StatisticConfiguration::byUser()->where('enterprise_id',$request->get('emp_id'))->get());
        */
        return response(StatisticConfiguration::byUser()->filter($request->all())->get());

        //return response()->json(StatisticConfiguration::all()->toArray());
    }
 
    public function get(Request $request,$id)
    {
        $config = StatisticConfiguration::find($id);
        $fields = $config->fields;
        if(!isset($fields)) $fields = [];
        $tfields = [];
        foreach ($fields as $field) 
        {
            $type = ProductionType::find($field['field_type']);
            $field['field_type_name'] = '';
            if($type) $field['field_type_name'] = $type->name;
            $tfields[] = $field;
        }
        $config->fields = $tfields;
        return response($config);
    }

    public function insert(Request $request)
    {
        try {
            $config = new StatisticConfiguration();
            $config->type = $request->type;
            $config->enterprise_id = $request->enterprise_id;
            $config->meter_id = $request->meter_id;
            $config->name = $request->name;
            $config->color = $request->color;
            $config->chart_type = $request->chart_type;
            $config->chart_interval_daily = $request->chart_interval_daily;
            $config->chart_interval_weekly = $request->chart_interval_weekly;
            $config->fields = $request->fields;
            $config->save();
            return response($config);
            
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
             ],500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
        
            $config = StatisticConfiguration::find($id);
            $config->enterprise_id = $request->enterprise_id;
            $config->meter_id = $request->meter_id;
            $config->name = $request->name;
            $config->color = $request->color;
            $config->chart_type = $request->chart_type;
            $config->chart_interval_daily = $request->chart_interval_daily;
            $config->chart_interval_weekly = $request->chart_interval_weekly;
            $config->fields = $request->fields;
            $config->save();
            return response($config);

        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
             ],500);
        }
    }
    
    public function delete(Request $request, $id)
    {
        try {
            $config = StatisticConfiguration::find($id);
            $config->delete();
            return response([]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
             ],500);
        }
    }
    
    public function resume(Request $request,$id)
    {
        $config = StatisticConfiguration::find($id);
        
        return response(array(
            //'config'=>$config,
            'data' =>DataHelper::format_data($config) 
        ));
    }
    

    /**Manual Data **/
    
    public function manual_get_config(Request $request,$id)
    {
        $response = ManualConfiguration::where('enterprise_id',$id)->first();
        
        if(!$response)
        {
            $response = new ManualConfiguration();
            $response->data = [
                'hostname'=>'',
                'database'=>'',
                'port'=>'',
                'username'=>'',
                'password'=>'',
                'table'=>'',
            ];
        }
        return response($response->data);
    }

    public function manual_save_config(Request $request,$id)
    {
        try {
            $response = ManualConfiguration::where('enterprise_id',$id)->first();
        
            $db = DataHelper::makeHostConnection($request->hostname,$request->port,$request->username,$request->password);
            $tb = $db->table("information_schema.tables")->select("table_name")->where("table_schema", $request->database)->first();
            
            if(!$response)
            {
                $response = new ManualConfiguration();
                $response->enterprise_id = $id;
            }
            $response->data = [
                'hostname'=>$request->hostname,
                'database'=>$request->database,
                'port'=>$request->port,
                'username'=>$request->username,
                'password'=>$request->password,
                'table'=>$request->table,
            ];
            $response->save();
            return response($response);   
        } catch (Exception $ex) {
            return response('No se pudo validar la conexion a BD con los datos especificados.'.$ex->getMessage(),500);   
        }
    }

    public function manual_get_tables(Request $request,$id)
    {
        try {
            $config = ManualConfiguration::where('enterprise_id',$id)->first();
            if(!$config)
            {
                $config = new ManualConfiguration();
                $config->data = [
                    'hostname'=>'',
                    'database'=>'',
                    'port'=>'',
                    'username'=>'',
                    'password'=>'',
                    'table'=>'',
                ];
                return response('Se debe definir la configuración por parte del administrador',449);   
            }
            $db = DataHelper::makeHostConnection($config->data['hostname'],$config->data['port'],$config->data['username'],$config->data['password']);
            $tables = $db->table("information_schema.tables")->select("table_name as name")->where("table_schema", $config->data['database'])->get();
            
            return response($tables->toArray());   
        } catch (Exception $ex) {
            return response($ex->getMessage(),500);   
        }
    }

    public function manual_get_fields(Request $request,$id,$table)
    {
        try {
            $config = ManualConfiguration::where('enterprise_id',$id)->first();
            if(!$config)
            {
                $config = new ManualConfiguration();
                $config->data = [
                    'hostname'=>'',
                    'database'=>'',
                    'port'=>'',
                    'username'=>'',
                    'password'=>'',
                    'table'=>'',
                ];
            }
            $db = DataHelper::makeHostConnection($config->data['hostname'],$config->data['port'],$config->data['username'],$config->data['password']);
            $fields = $db->table("information_schema.columns")->select("column_name as name")->where("table_schema", $config->data['database'])
                                                                                ->where("TABLE_NAME", $table)->get();
            return response($fields->toArray());   
        } catch (Exception $ex) {
            return response($ex->getMessage(),500);   
        }
    }

    public function manual_update_field_name(Request $request,$id,$table,$field)
    {
        try {
            $config = ManualConfiguration::where('enterprise_id',$id)->first();
            $db = DataHelper::makeHostConnection($config->data['hostname'],$config->data['port'],$config->data['username'],$config->data['password']);
            $type = $db->select("SELECT column_type from INFORMATION_SCHEMA.COLUMNS where 
                    table_schema = '".$config->data['database']."' and TABLE_NAME = '".$table."' and COLUMN_NAME = '".$field."' ")[0]; 
            
            
            $db->statement("ALTER TABLE `".$config->data['database']."`.`".$table."` CHANGE ".$field." ".$request->name." ".$type->column_type.";");
            return response('OK');   
        } catch (Exception $ex) {
            return response($ex->getMessage(),500);   
        }
    }
    
    public function manual_get_fields_values(Request $request,$id,$table,$field)
    {
       
        try {
            $config = ManualConfiguration::where('enterprise_id',$id)->first();
            $db = DataHelper::makeHostConnection($config->data['hostname'],$config->data['port'],$config->data['username'],$config->data['password']);
            $data = [];
            $tquery = "";
            switch ($request->type) {
                case 'day':
                    //$tquery = "select date,format(`".$field."`,3,'de_DE') as field from `".$config->data['database']."`.`".$table."` where  year(`date`) = '".$request->year."' and month(`date`) = '".$request->month."'";
                    for ($i=1; $i <= 31; $i++) {
                        $m = $i > 9 ? $i : "0$i";
                        if(!empty($tquery)) $tquery .= "\n union \n";
                        $tquery .= "select '".$request->year."-".$request->month."-$m' as date ,format(sum(`".$field."`),3,'de_DE') as field from `".$config->data['database']."`.`".$table."` where  year(`date`) = '".$request->year."' and month(`date`) = '".$request->month."' and day(`date`) = '$i'";
                    }
                    break;
                case 'month':
                    
                    for ($i=1; $i <= 12; $i++) {
                        $m = $i > 9 ? $i : "0$i";
                        if(!empty($tquery)) $tquery .= "\n union \n";
                        $tquery .= "select '".$request->year."-".$m."-01' as date ,format(sum(`".$field."`),3,'de_DE') as field from `".$config->data['database']."`.`".$table."` where  year(`date`) = '".$request->year."' and month(`date`) = '".$i."'";
                    }
                    break;
                case 'year':
                    //$data = $db->select("select date,`".$field."` as field from `".$config->data['database']."`.`".$table."` where  year(`date`) = '".$request->year."' and month(`date`) = '".$request->month."'");
                    
                    
                    for ($i=intval($request->year) - 3 ; $i <= intval($request->year) + 2; $i++) {
                        if(!empty($tquery)) $tquery .= "\n union \n";
                        $tquery .= "select '".$i."-01-01' as date,format(sum(`".$field."`),3,'de_DE') as field from `".$config->data['database']."`.`".$table."` where  year(`date`) = '".$i."'";
                    }

                    break;
            }
            //echo $tquery;
            $data = $db->select($tquery);
            /*if(!$config->meta) $config->meta = [];
            $mkey = $table.'!'.$field.'!'.$request->type.'!'.$request->current;
            if(array_key_exists($mkey,$config->meta)) $data = $config->meta[$mkey];*/
            return response($data);   
        } catch (Exception $ex) {
            return response($ex->getMessage(),500);   
        }
    }

    public function manual_save_fields_values(Request $request,$id,$table,$field)
    {
        try {
            $config = ManualConfiguration::where('enterprise_id',$id)->first();
            
            $db = DataHelper::makeHostConnection($config->data['hostname'],$config->data['port'],$config->data['username'],$config->data['password']);
           
            $meta = [];
            if($config->meta) $meta = $config->meta;
            $mkey = $table.'!'.$field.'!'.$request->type.'!';
            $meta[$mkey] = $request->data;
            switch ($request->type) {
                case 'day':
                    foreach ($request->data as $day => $value) 
                    {
                        if($value == '') $value = 0;
                        $value = str_replace(',','.',str_replace('.','',$value) );
                        DataHelper::saveDayValues($db,$config->data['database'],$table,$field,$day,$value);
                    }
                    break;
                case 'month':
                    foreach ($request->data as $month => $value) 
                    {
                        $firstDateMonth = new Carbon($month);
                        
                        if($value == '') $value = 0;
                        $value = str_replace(',','.',str_replace('.','',$value) );
                        $d_value = $value / $firstDateMonth->daysInMonth;
                        
                        
                        for ($i=1; $i <= $firstDateMonth->daysInMonth ; $i++) 
                        {
                            $d = $i > 9 ? $i : "0$i";
                            DataHelper::saveDayValues($db,$config->data['database'],$table,$field,$firstDateMonth->format('Y-m').'-'.$d,$d_value);
                        }
                    }
                    break;
                case 'year':
                    foreach ($request->data as $year => $value) 
                    {
                        $date = new Carbon($year);
                        
                        if($value == '') $value = 0;
                        $value = str_replace(',','.',str_replace('.','',$value) );
                        $d_value = $value / 365;
                        for ($i=1; $i <= 365 ; $i++) 
                        {
                            DataHelper::saveDayValues($db,$config->data['database'],$table,$field,$date->format('Y-m-d'),$d_value);
                            $date->addDay();
                        }
                    }
                    
                    break;
            }
            
            $config->meta = $meta;
            $config->save();
            return response('ok');   
        } catch (Exception $ex) {
            throw $ex;
            return response($ex->getMessage(),500);   
        }
    }

    
    public function migration()
    {
        $confs = ProductionConfiguration::all();
        StatisticConfiguration::truncate();
        foreach ($confs as $conf) 
        {
            $stats = new StatisticConfiguration();
            $meter = EnergyMeter::find($conf->meter_id);
            $stats->type = 'produccion';
            $stats->enterprise_id = $conf->enterprise_id;
            $stats->meter_id = $conf->meter_id;
            $stats->name = $conf->name;
            $stats->color = $conf->color;
            $stats->chart_type = $conf->chart_type;
            $stats->chart_interval_daily = $conf->chart_interval_daily;
            $stats->chart_interval_weekly = $conf->chart_interval_weekly;
            
            $fields = [];
            $field_id = 1;
            foreach ($conf->production_group_fields as $field) 
            {
                $db_fields_reference = [];
                $db_fields = [];
                $expresion = '';
                foreach ($field->operands as $operand) 
                {
                    switch ($operand->field_type_id) {
                        case '1':
                            $tfield = ProductionField::where('configuration_id',$field->configuration_id)->where('name',$operand->field_content)->first();
                            $expresion = StatisticsApiController::migration_fields($tfield,$db_fields_reference,collect($meter->production_databases));
                            break;
                        case '2':
                            if($operand->field_database && $operand->field_table )
                            {
                                $db = collect($meter->production_databases)->where('id', $operand->field_database)->first();
                                $key = $db['name'].'.'.$operand->field_table.'.'.$operand->field_content;
                                if(!array_key_exists($key,$db_fields_reference))
                                {
                                    $db_fields_reference[$key] = [
                                        "id"=>count($db_fields_reference) + 1,
                                        "key"=>'avg('.$key.')',
                                        "field"=>$operand->field_content,
                                        "table"=>$operand->field_table,
                                        "group_by"=>"avg",
                                        "connection"=>$db['id']
                                    ];
                                    
                                }
                                $expresion = 'avg('.$key.')';
                            }
                            break;
                        case 3:
                            $expresion = $operand->field_content;
                            break;
                    } 
                }
                foreach ($db_fields_reference as $key => $dbfield) 
                {
                    $db_fields[] = $dbfield;
                }
               
                $destiny = [];
                switch ($field->show_type_id) {
                    case '1':
                        $destiny = ['1'];
                        break;
                    case '2':
                        $destiny = ['2'];
                        break;
                    case '3':
                        $destiny = ['1','2'];
                        break;
                    case '4':
                        $destiny = ['3'];
                        break;
                    case '5':
                        $destiny = ['1','3'];
                        break;
                    case '6':
                        $destiny = ['2','3'];
                        break;
                    case '7':
                        $destiny = ['1','2','3','4'];
                        break;
                }
                $f = [
                    "id"=> $field_id, 
                    "name"=>$field->name,
                    "display_name" => $field->display_name, 
                    "color" => $field->color, 
                    "field_type" => $field->production_type_id,
                    "destiny" => $destiny, 
                    "operation_type" => $field->operation_id, 
                    "number_type"=> $field->number_type_id, 
                    "decimals" => $field->decimal_count, 
                    "unities" => $field->units,
                    "expression"=> $expresion,
                    "database_fields" => $db_fields
                ];
                $fields[] = $f;
                $field_id ++;
            }
            $stats->fields = $fields;
            $stats->save();
        }
        echo 'Migration completada';
    }

    private static function migration_fields($field,&$lst_fields,$databases)
    {
        $op = $field->operation->parser_format;
        
        $expresion = '';
        foreach ($field->operands as $operand) 
        {
            $content = '';
            switch ($operand->field_type_id) {
                case 1:
                    $r_field = ProductionField::where('configuration_id',$field->configuration_id)->where('name',$operand->field_content)->first();
                    $content = StatisticsApiController::migration_fields($r_field,$lst_fields,$databases);
                    break;
                case 2:
                    if($operand->field_database && $operand->field_table )
                    {
                        $db = $databases->where('id', $operand->field_database)->first();
                        $key = $db['name'].'.'.$operand->field_table.'.'.$operand->field_content;
                        if(!array_key_exists($key,$lst_fields))
                        {
                            $lst_fields[$key] = [
                                "id"=>count($lst_fields) + 1,
                                "key"=>'avg('.$key.')',
                                "field"=>$operand->field_content,
                                "table"=>$operand->field_table,
                                "group_by"=>"avg",
                                "connection"=>$db['id']
                            ];
                            
                        }
                        $content = 'avg('.$key.')';
                    }
                    
                    break;
                case 3:
                    $content = $operand->field_content;
                    break;
            }
            if(!empty($content))
            {
                if(empty($expresion) || $op == '( _XXX_ )')  
                    $expresion = $content;
                else
                    $expresion .= $op . $content;
            }
            
        }
        return $expresion;
    }
  
    /*
        
        public function config_api_get(Request $request,$id)
        {
            
        }


        public function config_api_resume(Request $request,$id)
        {
            $config = IndicatorConfiguration::find($id);
            
            return response(array(
                //'config'=>$config,
                'data' =>DataHelper::format_data($config) 
            ));
        }

        public function config_list(Request $request)
        {
            return view('indicators.config.list',array('user'=>Auth::user(),'tipo_count'=>null));
        }

        public function config_insert(Request $request)
        {
            return view('indicators.config.insert',array('user'=>Auth::user(),'tipo_count'=>null));
        }

        public function config_update(Request $request,$id)
        {
            return view('indicators.config.update',array('user'=>Auth::user(),'tipo_count'=>null,'id'=>$id));
        }


        public function resume(Request $request)
        {
            $user = Auth::user();
            
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
            $dataRequest["interval"] = $interval;
            $dataRequest["flash_current_count"] = $flash_current_count;
            
            $contador = ContadorController::getCurrrentController($dataRequest);
            
            $interval = "";
            $flash_current_count = null;
            $session = $request->session()->get('_flash');
            if(array_key_exists('intervalos', $session))
            {
                $interval = $session['intervalos'];
                if(array_key_exists("current_count", $session))
                {
                    $flash_current_count = $session['current_count'];
                }
            }
            
            $flash = Session::get('_flash');
            $dataHandler = new ProductionDataHandlerController();
            if(array_key_exists("date_from_personalice", $flash)){
                $date_from = $flash['date_from_personalice'];
            }
            
            if(!isset($date_from)){
                $dateInfo = $dataHandler->getDatesAnalysis();
                $date_from = $dateInfo["date_from"];
                $date_to = $dateInfo["date_to"];
                $label_intervalo = $dateInfo["date_label"];
            } else {
                $flash = Session::get('_flash');
                
                $date_to = Session::get('_flash')['date_to_personalice'];
                if(array_key_exists("label_intervalo_navigation", $flash)){
                    $dateInfo = $dataHandler->getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                } else {
                    $dateInfo = $dataHandler->getDatesAnalysis();
                    $label_intervalo = $dateInfo["date_label"];
                }
            }   


            
            $userEnterprice = EnterpriseUser::where("user_id", $user->id)->first();
            $configs = IndicatorConfiguration::where("enterprise_id", $userEnterprice->enterprise_id)->where('meter_id',$contador->id)->get();
            
            return view('indicators.resume',array('user'=>Auth::user(),
                                                'titulo'=>'Indicadores',
                                                'label_intervalo'=>$label_intervalo,
                                                'date_from'=>$date_from,
                                                'date_to'=>$date_to,
                                                'configurations'=>$configs,
                                                'contador2'=>$contador,
                                                'tipo_count'=>$contador->tipo));
        }
    */    
}
