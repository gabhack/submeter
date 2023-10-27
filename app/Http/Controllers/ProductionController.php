<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Enterprise;
use App\ProductionConfiguration;
use App\ProductionGroupField;
use App\ProductionFieldOperand;
use App\ProductionGroupOperand;
use App\User;
use App\EnterpriseUser;
use Session;

use App\ProductionField;
use App\EnergyMeter;
class ProductionController extends Controller
{
    public function list()
    {
        # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se cargen sus configuraciones
        $productions = ProductionConfiguration::byUser()->get();
        $user = Auth::user();
        $tipo_count = null;
        
        return view("production.list", compact("productions", "tipo_count" ,"user"));
    }
    
    public function create()
    {
        $user = Auth::user();
        $tipo_count = null;
        
        $enterprises = Enterprise::byUser()->get(); # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se carge su empresa
        $energy_meters = EnergyMeter::byUser()->orderBy('count_label')->get(); # @Leo W* crear scope para en caso de que el usuario no sea admin, los energymeters relacionados
        $chartTypes = [ # @Leo W* array para los tipos de graficos que vamos a usar
            'line' => 'Línea',
            'bar' => 'Barra', 
            'area' => 'Area',
            'pie' => 'Pie',
            'column' => 'Columna'
        ];
        $url_return = route("production.list");
        $titulo = "Producción Submetering";
        return view("production.create", compact("enterprises", "energy_meters" ,"titulo", "tipo_count", "url_return", "user","chartTypes"));
    }
    
    public function show(Request $request, $production_id)
    {
        $user = Auth::user();
        $tipo_count = null;
        
        $enterprises = Enterprise::byUser()->get(); # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se carge su empresa
        $energy_meters = EnergyMeter::byUser()->orderBy('count_label')->get(); # @Leo W* crear scope para en caso de que el usuario no sea admin, los energymeters relacionados
        $production = ProductionConfiguration::find($production_id);    
        $chartTypes = [ # @Leo W* array para los tipos de graficos que vamos a usar
            'line' => 'Línea',
            'bar' => 'Barra', 
            'area' => 'Area',
            'pie' => 'Pie',
            'column' => 'Columna'
        ];    
        
        
        $url_return = route("production.list");
        $titulo = "Producción Submetering";
        return view("production.show", compact("production", "enterprises", "energy_meters", "titulo", "tipo_count", "url_return", "user","chartTypes"));
    }
    
    public function exportCSV(Request $request)
    {
        $tipo_count = null;
        
        $production_id = $request->get("production_id", -1);        
        
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
        
        $production = ProductionConfiguration::find($production_id);
        if($production)
        {
            $data = $dataHandler->getFormatedProductionData($production->id);
            if($data["error"])
            {
                $data = [];
                $data["error"] = true;
                return $data;
            }
            $data = $data["production"];
            
            $interval_keys = [];
            $interval_labels = [];
            if(array_key_exists("labels", $data))
            {
                $interval_keys = $data["labels"]["interval_keys"];
                $interval_labels = $data["labels"]["interval_values"];
            }
            
            $csv_idx = [1, 3, 6];
            $table_data = [];
            $dt = [];
            if( array_key_exists("group_date_table", $data) )
            {
                $dt = $data["group_date_table"];
            }
            
            $header_data = [];
            $header_data[] = "Fecha";
            foreach($data["group_data"] as $keyC => $config)
            {
                if( in_array($config["show_type"], [2, 3, 6, 7]) )
                {
                    $header_data[] = $config["display_name"];
                }
            }
            
            for($i = 0; $i < count($interval_keys); $i++)
            {
                $key = $interval_keys[$i];
                $label = $interval_labels[$i];
                $row_data = [];
                $row_data[] = $label;
                if(array_key_exists($key, $dt))
                {
                    $tdata = $dt[$key];
                    foreach($data["group_data"] as $keyC => $config)
                    {
                        if( in_array($config["show_type"], [2, 3, 6, 7]) && array_key_exists($keyC, $tdata) )
                        {
                            if($config["number_type"] == 2)
                            {
                                $number = number_format($tdata[$keyC],0,',','.');
                            }
                            else
                            {
                                $number = number_format($tdata[$keyC],$config["decimals"],',','.');
                            }
                            $number.= " ".$config["units"];
                            $row_data[] = $number;
                        }
                    }
                }
                $table_data[] = $row_data;
            }
            
            $csv_data = '"'.implode('","' , $header_data).'"'."\r\n";
            for($i = 0; $i < count($table_data); $i++)
            {
                $csv_data .= '"'.implode('","' , $table_data[$i]).'"'."\r\n";
            }
            
            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
                ,   'Content-type'        => 'text/csv'
                ,   'Content-Disposition' => 'attachment; filename=export.csv'
                ,   'Expires'             => '0'
                ,   'Pragma'              => 'public'
            ];
            return response($csv_data, 200, $headers);
        }
        
        $data = [];
        $data["error"] = true;
        return $data;
    }
    
    public function showProductionData(Request $request, $id,$counter_id = null)
    {

       /* dd($request);
        dd($id);
        die();*/
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


        
        $uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
        $productions = ProductionConfiguration::where("enterprise_id", $uEnterprise->enterprise_id)->where('meter_id',$contador2->id)->get();
       
        foreach($productions as $production)
        {
       
            //echo $productions;
            $data = $dataHandler->getFormatedProductionData($production->id);
            $production->data_production = $data;
          
        }
    
          //die();
        $titulo = "Producción Submetering";
        return view("production.display", compact("contador_label", "contador2", "date_from", "date_to", "label_intervalo", "productions", "user", 
            "titulo", "tipo_count", "user"));
    }
    
    public function saveProduction(Request $request)
    {
        $pconfiguration = new ProductionConfiguration;
        $pconfiguration->enterprise_id = $request->enterprise;
        $pconfiguration->name = $request->name;
        $pconfiguration->meter_id = $request->energymeter;
        $pconfiguration->database = $request->database;
        $pconfiguration->table_name = $request->table;
        $pconfiguration->color = $request->color;
        # @Leo W* dos nuevos campos en la configuracion de produccion 
        $pconfiguration->chart_type = $request->chart_type;
        $pconfiguration->chart_interval_daily = $request->chart_interval_daily;
        $pconfiguration->chart_interval_weekly = $request->chart_interval_weekly;

        $pconfiguration->save();
        
        $this->saveDetailsProduction($request, $pconfiguration);
        
        $request->session()->flash('message.production', 'Configuración de Producción Guardada');
        
        $data = [];
        $data["error"] = false;
        return $data;
    }
    
    public function updateProduction(Request $request, $production_id)
    {
        $pconfiguration = ProductionConfiguration::find($production_id);
        if($pconfiguration)
        {
            $pconfiguration->enterprise_id = $request->enterprise;
            $pconfiguration->name = $request->name;
            $pconfiguration->meter_id = $request->energymeter;
            $pconfiguration->database = $request->database;
            $pconfiguration->table_name = $request->table;
            $pconfiguration->color = $request->color;
            # @Leo W* dos nuevos campos en la configuracion de produccion 
            $pconfiguration->chart_type = $request->chart_type;
            $pconfiguration->chart_interval_daily = $request->chart_interval_daily;
            $pconfiguration->chart_interval_weekly = $request->chart_interval_weekly;

            $pconfiguration->save();
            
            $this->saveDetailsProduction($request, $pconfiguration);
        }
        
        $request->session()->flash('message.production', 'Configuración de Producción Actualizada');
        
        $data = [];
        $data["error"] = false;
        return $data;
    }
    
    private function saveDetailsProduction($request, $pconfiguration)
    {
        $fields_id = $request->get("fieldid", []);
        $fields_name = $request->get("fieldname", []);
        $fields_operation_type = $request->get("fieldoperationtype", []);
        
        $fields_group_id = $request->get("fieldgroupid", []);
        $fields_group_name = $request->get("groupname", []);
        $fields_group_name_show = $request->get("groupdisplayname", []);
        $fields_group_type = $request->get("fieldgrouptype", []);
        $fields_group_operation_type = $request->get("operationgrouptype", []);
        $fields_group_show = $request->get("fieldgroupshow", []);
        $fields_group_number_type = $request->get("numbergrouptype", []);
        $fields_group_units = $request->get("unitsgroup", []);
        $fields_group_decimals = $request->get("decimalsgroup", []);
        $fields_group_color = $request->get("fieldgroupcolor", []);
        
        $operands_id = $request->get("operand_id", []);
        $operands_type = $request->get("operand_type", []);
        $operands_value = $request->get("value_field", []);
        $operands_table = $request->get("value_table", []);
        $operands_const = $request->get("value_const", []);
        
        $operands_field_ddatabase = $request->get("field_database", []);
        $operands_field_dtable = $request->get("field_table", []);
        
        
        $operands_group_id = $request->get("operandgroup_id", []);
        $operands_group_type = $request->get("operandgroup_type", []);
        $operands_group_value = $request->get("valuegroup_field", []);
        $operands_group_table = $request->get("valuegroup_table", []);
        $operands_group_const = $request->get("valuegroup_const", []);

        $operands_group_ddatabase = $request->get("group_database", []);
        $operands_group_dtable = $request->get("group_table", []);
        
        ProductionField::where("configuration_id", $pconfiguration->id)->update(["updated" => 0]);
        
        for($i = 0; $i < count($fields_id); $i++)
        {
            $field = null;
            if(is_numeric($fields_id[$i]))
            {
                $field = ProductionField::find($fields_id[$i]);
            }
            
            if(!$field)
            {
                $field = new ProductionField;
                $field->configuration_id = $pconfiguration->id;
            }
            
            $field->name = $fields_name[$i];
            $field->operation_id = $fields_operation_type[$i];
            $field->updated = 1;
            $field->save();
            
            $field_id = $fields_id[$i];
            
            if(array_key_exists($field_id, $operands_id))
            {
                if(!array_key_exists($field_id, $operands_value))
                {
                    $operands_value[$field_id] = array_fill(0, count($operands_id[$field_id]), "");
                }
                
                if(!array_key_exists($field_id, $operands_table))
                {
                    $operands_table[$field_id] = array_fill(0, count($operands_id[$field_id]), "");
                }
                
                if(!array_key_exists($field_id, $operands_const))
                {
                    $operands_const[$field_id] = array_fill(0, count($operands_id[$field_id]), 0);
                }
                
                $op_id = $operands_id[$field_id];
                $op_type = $operands_type[$field_id];
                $op_value = $operands_value[$field_id];
                $op_table = $operands_table[$field_id];
                $op_const = $operands_const[$field_id];

                $op_field_database = array_key_exists($field_id,$operands_field_ddatabase) ? $operands_field_ddatabase[$field_id] : null; //leo w
                $op_field_table = array_key_exists($field_id,$operands_field_dtable) ? $operands_field_dtable[$field_id] : null; //leo w
                
                ProductionFieldOperand::where("production_field_id", $field->id)->update(["updated" => 0]);
                for($j = 0; $j < count($op_id); $j++)
                {
                    $operand = null;
                    if(is_numeric($op_id[$j]))
                    {
                        $operand = ProductionFieldOperand::find($op_id[$j]);
                    }
                    
                    if(!$operand)
                    {
                        $operand = new ProductionFieldOperand;
                        $operand->production_field_id = $field->id;
                    }
                    $operand->field_type_id = $op_type[$j];
                    if($operand->field_type_id == 1)
                    {
                        $operand->field_content = $op_value[$j];
                    }
                    else if($operand->field_type_id == 2)
                    {
                        $operand->field_content = $op_table[$j];
                        if(is_array($op_field_database) && array_key_exists($j,$op_field_database) && $op_field_database[$j]) $operand->field_database = $op_field_database[$j];
                        if(is_array($op_field_table) && array_key_exists($j,$op_field_table) && $op_field_table[$j]) $operand->field_table = $op_field_table[$j];
                    }
                    else if($operand->field_type_id == 3)
                    {
                        $operand->field_content = $op_const[$j];
                    }
                    else
                    {
                        $operand->field_content = "";
                    }
                    $operand->updated = 1;
                    $operand->save();
                }
                ProductionFieldOperand::where("production_field_id", $field->id)->where("updated", 0)->delete();
            }
            
        }
        ProductionField::where("configuration_id", $pconfiguration->id)->where("updated", 0)->delete();
        
        ProductionGroupField::where("configuration_id", $pconfiguration->id)->update(["updated" => 0]);
        
        for($i = 0; $i < count($fields_group_id); $i++)
        {
            $field_group = null;
            if(is_numeric($fields_group_id[$i]))
            {
                $field_group = ProductionGroupField::find($fields_group_id[$i]);
            }
            
            if(!$field_group)
            {
                $field_group = new ProductionGroupField;
                $field_group->configuration_id = $pconfiguration->id;
            }
            
            $field_group->name = $fields_group_name[$i];
            $field_group->display_name = ($fields_group_name_show[$i])?$fields_group_name_show[$i]:"";
            $field_group->production_type_id = $fields_group_type[$i];
            $field_group->show_type_id = $fields_group_show[$i];
            $field_group->operation_id = $fields_group_operation_type[$i];
            $field_group->number_type_id = $fields_group_number_type[$i];
            $field_group->decimal_count = ($fields_group_decimals[$i])?$fields_group_decimals[$i]:0;
            $field_group->units = $fields_group_units[$i];
            $field_group->color = $fields_group_color[$i];
            $field_group->updated = 1;
            $field_group->save();
            
            $field_group_id = $fields_group_id[$i];
            
            if(array_key_exists($field_group_id, $operands_group_id))
            {
                if(!array_key_exists($field_group_id, $operands_group_value))
                {
                    $operands_group_value[$field_group_id] = array_fill(0, count($operands_group_id[$field_group_id]), "");
                }
                
                if(!array_key_exists($field_group_id, $operands_group_table))
                {
                    $operands_group_table[$field_group_id] = array_fill(0, count($operands_group_id[$field_group_id]), "");
                }
                
                if(!array_key_exists($field_group_id, $operands_group_const))
                {
                    $operands_group_const[$field_group_id] = array_fill(0, count($operands_group_id[$field_group_id]), 0);
                }
                
                $op_group_id = $operands_group_id[$field_group_id];
                $op_group_type = $operands_group_type[$field_group_id];
                $op_group_value = $operands_group_value[$field_group_id];
                $op_group_table = $operands_group_table[$field_group_id];
                $op_group_const = $operands_group_const[$field_group_id];

                $op_group_ddatabase = array_key_exists($field_group_id,$operands_group_ddatabase) ? $operands_group_ddatabase[$field_group_id] : null; //leo w
                $op_group_dtable =  array_key_exists($field_group_id,$operands_group_dtable) ? $operands_group_dtable[$field_group_id] : null; //leo w
                
                ProductionGroupOperand::where("production_group_field_id", $field_group->id)->update(["updated" => 0]);
                
                for($j = 0; $j < count($op_group_id); $j++)
                {
                    $operand = null;
                    if(is_numeric($op_group_id[$j]))
                    {
                        $operand = ProductionGroupOperand::find($op_group_id[$j]);
                    }
                    
                    if(!$operand)
                    {
                        $operand = new ProductionGroupOperand;
                        $operand->production_group_field_id = $field_group->id;
                    }
                    $operand->field_type_id = $op_group_type[$j];
                    if($operand->field_type_id == 1)
                    {
                        $operand->field_content = $op_group_value[$j];
                    }
                    else if($operand->field_type_id == 2)
                    {
                        $operand->field_content = $op_group_table[$j];
                        if(is_array($op_group_ddatabase) && $op_group_ddatabase[$j]) $operand->field_database = $op_group_ddatabase[$j];
                        if(is_array($op_group_dtable) && $op_group_dtable[$j]) $operand->field_table = $op_group_dtable[$j];
                    }
                    else if($operand->field_type_id == 3)
                    {
                        $operand->field_content = $op_group_const[$j];
                    }
                    else
                    {
                        $operand->field_content = "";
                    }
                    $operand->updated = 1;
                    $operand->save();
                }
                
                ProductionGroupOperand::where("production_group_field_id", $field_group->id)->where("updated", 0)->delete();
            }
            
        }
        
        ProductionGroupField::where("configuration_id", $pconfiguration->id)->where("updated", 0)->delete();
    }


   
    
    public function removeProduction(Request $request, $id)
    {
        $production = ProductionConfiguration::find($id);
        if($production)
        {
            $production->delete();
        }
        $data = [];
        $data["error"] = false;
        return $data;
    }
    
    public function readDataBase(Request $request)
    {
        try
        {
            $energy_meter_id = $request->energymeter;
            //$meter = EnergyMeter::find($energy_meter_id);
            $meter = EnergyMeter::orderBy('count_label')->get();
            //$meter = EnergyMeter::take(15)->get();
           /* return $meter;
            die();*/
            $databases_data = [];
            if($meter)
            {
                    foreach ($meter as $meterData) 
                    {
                          try
                                    {

                                        //\DB::purge('mysql2');
                                        //\DB::reconnect('mysql2');
                                        \DB::purge('mysql2');
                                        config(['database.connections.mysql2.host' => $meterData->host]);
                                        config(['database.connections.mysql2.port' => $meterData->port]);
                                        config(['database.connections.mysql2.username' => $meterData->username]);
                                        config(['database.connections.mysql2.password' => $meterData->password]);
                                        env('MYSQL2_HOST',$meterData->host);
                                        env('MYSQL2_USERNAME', $meterData->username);
                                        env('MYSQL2_PASSWORD',$meterData->password);
                                        \DB::connection('mysql2')->getPdo();



                                                    $db = \DB::connection('mysql2');    
                                                    $database_meter = $meterData->database;  


                                                    $databases = $db->table('information_schema.schemata')->select(\DB::raw("`SCHEMA_NAME` database_name"))->get();                                       
                                                     
                                       
                                                        foreach($databases as $database)
                                                        {

                                                            $database_data = [];
                                                            
                                                            $database_name = $database->database_name;                                        
                                                            /*if($database_name != $database_meter)
                                                            {
                                                                continue;
                                                            }*/
                                                            if($database_name == "information_schema" || $database_name == "mysql")
                                                            {
                                                                continue;
                                                            }
                                                            $database_data["count_label"] = $meterData->count_label;
                                                            $database_data["name"] = $database_name;
                                                            $database_data["tables"] = [];
                                                            $tables = $db->table("information_schema.tables")->select("table_name")->where("table_schema", $database_name)->get();
                                                            foreach($tables as $table)
                                                            {
                                                                $table_data = [];
                                                                $table_name = $table->table_name;
                                                                $table_data["table_name"] = $table_name;
                                                                $table_data["fields"] = [];                    
                                                                
                                                                $fields = $db->table("information_schema.columns")->select("column_name")->where("table_schema", $database_name)
                                                                                ->where("TABLE_NAME", $table_name)->get();
                                                                foreach($fields as $field)
                                                                {
                                                                    $field_data = [];
                                                                    $field_name = $field->column_name;
                                                                    $field_data["name"] = $field_name;
                                                                    $table_data["fields"][] = $field_data;
                                                                }
                                                                
                                                                $database_data["tables"][] = $table_data;
                                                            }
                                                            $databases_data[] = $database_data;
                                                            
                                                        }


                                            
                              }catch (\Exception $e)
                                {
                                    continue;
                                }                                  
                                                                           
                                       
                    }
                           
            }
            $data = [];
            $data["error"] = false;            
            $data["databases"] = $databases_data;
            return $data;
        }
        catch (\Exception $e)
        {
            $data = [];
            $data["error"] = true;
            return $data;
        }
        
    }


    public function getDatabases(Request $request)
    {
        $response = [
            'data'=>[]
        ];
        try
        {
            \DB::purge('mysql2');
            config(['database.connections.mysql2.host' => $request->input('host')]);
            config(['database.connections.mysql2.port' => $request->input('port')]);
            config(['database.connections.mysql2.username' => $request->input('username')]);
            config(['database.connections.mysql2.password' => $request->input('password')]);
            env('MYSQL2_HOST',$request->input('host'));
            env('MYSQL2_USERNAME', $request->input('username'));
            env('MYSQL2_PASSWORD',$request->input('password'));
            \DB::connection('mysql2')->getPdo();
            $db = \DB::connection('mysql2');    
            $databases = $db->table('information_schema.schemata')->select(\DB::raw("`SCHEMA_NAME` database_name"))->get();                                       
                                       
            foreach($databases as $database)
            {
                $db_name = $database->database_name;                                        
                /*if($database_name != $database_meter)
                {
                    continue;
                }*/
                if($db_name == "information_schema" || $db_name == "mysql")
                {
                    continue;
                }
                $response['data'][] = $db_name;
                
            }
        }
        catch (\Exception $e)
        {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }

    public function getTables(Request $request,$meter_id,$database)
    {
        $response = [
            'data'=>[]
        ];
        try
        {
            $meter = EnergyMeter::find($meter_id);
            if($meter == null) return $response;

            \DB::purge('mysql2');
            config(['database.connections.mysql2.host' => $meter->host]);
            config(['database.connections.mysql2.port' => $meter->port]);
            config(['database.connections.mysql2.username' => $meter->username]);
            config(['database.connections.mysql2.password' => $meter->password]);
            env('MYSQL2_HOST',$meter->host);
            env('MYSQL2_USERNAME', $meter->username);
            env('MYSQL2_PASSWORD',$meter->password);
            \DB::connection('mysql2')->getPdo();
            $db = \DB::connection('mysql2');    
            
            $tables = $db->table("information_schema.tables")->select("table_name")->where("table_schema", $database)->get();
            foreach($tables as $table)
            {
                $table_name = $table->table_name;
                
                $table_data["table_name"] = $table_name;
                $table_data["fields"] = [];                    
                
                $fields = $db->table("information_schema.columns")->select("column_name")->where("table_schema", $database)
                                ->where("TABLE_NAME", $table_name)->get();
                foreach($fields as $field)
                {
                    $field_data = [];
                    $field_name = $field->column_name;
                    $field_data["name"] = $field_name;
                    $table_data["fields"][] = $field_data;
                }
                
                $response['data'][] = $table_data;
            }

        }
        catch (\Exception $e)
        {
            $response["error"] = true;
        }
        return $response;
    }

    public function getCounterTables(Request $request,$counter_id,$id)
    {
        $response = [
            'data'=>[]
        ];
        try
        {
            $meter = EnergyMeter::find($counter_id);
            
            if($meter == null) return $response;
            
            foreach ($meter->production_databases as $conn) 
            {
                $database_data = [
                    'tables' => []
                ];
                if($conn['id'] == $id)
                {
                    \DB::purge('mysql2');
                    config(['database.connections.mysql2.host' => $conn['host']]);
                    config(['database.connections.mysql2.port' => $conn['port']]);
                    config(['database.connections.mysql2.username' => $conn['username']]);
                    config(['database.connections.mysql2.password' => $conn['password']]);
                    env('MYSQL2_HOST',$conn['host']);
                    env('MYSQL2_USERNAME', $conn['username']);
                    env('MYSQL2_PASSWORD',$conn['password']);
                    \DB::connection('mysql2')->getPdo();
                    $db = \DB::connection('mysql2');        
                    
                    //tables    
                    $tables = $db->table("INFORMATION_SCHEMA.COLUMNS")->select(\DB::raw("DISTINCT table_name"))->where("table_schema", $conn['database'])->where('COLUMN_NAME','date')->get();
                    foreach($tables as $table)
                    {
                        $table_name = $table->table_name;
                        
                        $table_data["table_name"] = $table_name;
                        $table_data["fields"] = [];                    
                        
                        $fields = $db->table("information_schema.columns")->select("column_name")->where("table_schema", $conn['database'])
                                        ->where("TABLE_NAME", $table_name)->get();
                        foreach($fields as $field)
                        {
                            $field_data = [];
                            $field_name = $field->column_name;
                            $field_data["name"] = $field_name;
                            $table_data["fields"][] = $field_data;
                        }
                        
                        $database_data['tables'][] = $table_data;
                    }
                    $response['data'][] = $database_data;
                }
            }
        }
        catch (\Exception $e)
        {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }
    
    public function getCounters(Request $request,$enterprise)
    {
        $response = [];
        $e = Enterprise::find($enterprise);
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

    public function getConnections(Request $request,$counter)
    {
        $response = [];
        $e = EnergyMeter::find($counter);
        if($e == null) return $response;
        foreach ($e->production_databases as $conn) 
        {
            if(is_array($conn) && array_key_exists('id',$conn))
            {
                $conn_data = [
                    'id' => $conn['id'],
                    'name' => $conn['name'],
                    'tables' => []
                ];

                \DB::purge('mysql2');
                config(['database.connections.mysql2.host' => $conn['host']]);
                config(['database.connections.mysql2.port' => $conn['port']]);
                config(['database.connections.mysql2.username' => $conn['username']]);
                config(['database.connections.mysql2.password' => $conn['password']]);
                env('MYSQL2_HOST',$conn['host']);
                env('MYSQL2_USERNAME', $conn['username']);
                env('MYSQL2_PASSWORD',$conn['password']);
                \DB::connection('mysql2')->getPdo();
                $db = \DB::connection('mysql2');        
                
                //tables    
                $tables = $db->table("INFORMATION_SCHEMA.COLUMNS")->select(\DB::raw("DISTINCT table_name"))->where("table_schema", $conn['database'])->where('COLUMN_NAME','date')->get();
                foreach($tables as $table)
                {
                    $table_name = $table->table_name;
                    
                    $table_data["name"] = $table_name;
                    $table_data["fields"] = [];                    
                    
                    $fields = $db->table("information_schema.columns")->select("column_name")->where("table_schema", $conn['database'])
                                    ->where("TABLE_NAME", $table_name)->get();
                    foreach($fields as $field)
                    {
                        $field_data = [];
                        $field_name = $field->column_name;
                        $field_data["name"] = $field_name;
                        $table_data["fields"][] = $field_data;
                    }
                    
                    $conn_data['tables'][] = $table_data;
                }

                //$conn_data['tables'][] =  $database_data;
                $response[] = $conn_data;
            }
        }
        return $response;
    }

   

}
