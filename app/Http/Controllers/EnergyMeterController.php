<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EnergyMeter;
use App\User;
use App\CurrentCount;
use Validator;
use Auth;

class EnergyMeterController extends Controller
{
    public function list(Request $request)
    {
        $columnNames = [0=>["id", true], 1 => ["count_label", true], 2 => ["edit", false], 3=>["delete", false]];
        $columns = $request["columns"];
        $search = $request["search"];
        $orders = $request["order"];
        $startData = $request["start"];
        $lengthData = $request["length"];
        $dataList = EnergyMeter::take(1e9);
        
        if(strlen($search["value"]) > 0)
        {
            foreach($columns as $index => $column)
            {
                if($column["searchable"] == "true" && $columnNames[$index][1])
                {
                    $dataList->orWhere($columnNames[$index][0], "LIKE", "%".$search["value"]."%");
                }
            }
        }
        
        foreach($orders as $index => $order)
        {
            $dataList->orderBy($columnNames[$order["column"]][0], $order["dir"]);
        }
        
        $total = $dataList->count();
        if($startData > 0)
        {
            $dataList->skip($startData);
        }
        $dataList->take($lengthData);
        
        $data = $dataList->get();
        //dd($data);
        //$data = EnergyMeter::all();
        $dataRows = [];
        foreach($data as $dat)
        {
            $dataRows[] = [$dat->id, $dat->count_label, 1, 1, 1];
        }
        
        $dataList = [];
        $dataList["draw"] = $request->get("draw");
        $dataList["recordsTotal"] = $total;
        $dataList["recordsFiltered"] = $total;
        $dataList["data"] = $dataRows;
        return $dataList;
    }
    
    public function listCombo(Request $request)
    {
        $dataRows = EnergyMeter::all();
        
        $dataList = [];
        $dataList["data"] = $dataRows;
        return $dataList;
    }
    
    public function show(Request $request, $meter_id)
    {
        $meter = EnergyMeter::find($meter_id);
        
        $data = [];
        $data["id"] = $meter->id;
        $data["name"] = $meter->count_label;
        $data["host"] = $meter->host;
        $data["database"] = $meter->database;
        $data["username"] = $meter->username;
        $data["password"] = $meter->password;
        $data["port"] = $meter->port;
        $data["type"] = $meter->tipo;
        $data["rate"] = $meter->tarifa;  
        $data["production_databases"] = $meter->production_databases;              
        $data["image"] = "";
        
        config(['database.connections.mysql2.host' => $meter->host]);
        config(['database.connections.mysql2.port' => $meter->port]);
        config(['database.connections.mysql2.database' => $meter->database]);
        config(['database.connections.mysql2.username' => $meter->username]);
        config(['database.connections.mysql2.password' => $meter->password]);
        env('MYSQL2_HOST',$meter->host);
        env('MYSQL2_DATABASE',$meter->database);
        env('MYSQL2_USERNAME', $meter->username);
        env('MYSQL2_PASSWORD', $meter->password);
        
        try {
            \DB::purge('mysql2');
            \DB::connection('mysql2')->getPdo();
            $db = \DB::connection('mysql2');
            
            $data_cliente = $db->table("Area_Cliente")->first();
            $data["contract"] = (property_exists($data_cliente, "TIPO_DE_CONTRATO_ENERGIA"))?$data_cliente->TIPO_DE_CONTRATO_ENERGIA:1;
            $data["profile"] = (property_exists($data_cliente, "PERFIL_USUARIO"))?$data_cliente->PERFIL_USUARIO:1;
            
            if(strlen($data_cliente->LOGOTIPO) > 0)
            {
                $image_route = public_path($data_cliente->LOGOTIPO);
                if(file_exists($image_route) && !is_dir($image_route))
                {
                    $data["image"] = asset($data_cliente->LOGOTIPO);
                }
            }
        }
        catch (\Exception $e) {
            $data = [];            
            $data["error"] = true;            
            return $data;
        }
        
        $dataSend = [];
        $dataSend["error"] = false;
        $dataSend["data"] = $data;
        return $dataSend;
    }
    
    public function save(Request $request)
    {
        $messages = [
            'name.required' => 'Debes agregar un nombre',
            'host.required' => 'Debes agregar un host',
            'database.required' => 'Debes agregar una base de datos',
            'username.required' => 'Debes agregar un usuario',
            'password.required' => 'Debes agregar una contraseña',
            'port.required' => 'Debes agregar un puerto',
            'port.numeric' => 'El puerto debe ser numerico',
            'rate.required' => 'Debes escoger una tarifa',
            'type.required' => 'Debes escoger un tipo',
            'contract.required' => 'Debes escoger un tipo de contrato',
        ];
        
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required|numeric',
            'rate' => 'required',
            'type' => 'required',
            'contract' => 'required'
        ],$messages);
        
        if ($v->fails())
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = $v->errors();
            return $data;
        }
        
        $medidor = new EnergyMeter;
        $medidor->count_label = $request->get("name");
        $medidor->host = $request->get("host");
        $medidor->port = $request->get("port");
        $medidor->database = $request->get("database");
        $medidor->username = $request->get("username");
        $medidor->password = $request->get("password");
		$medidor->subtipo = 0;
        $medidor->tipo = $request->get("type");
        $medidor->tarifa = $request->get("rate");
        $medidor->production_databases = $request->get("production_databases"); #@Leo W nuevo campo para el listado de base de datos de produccion
        $medidor->save();
        
        config(['database.connections.mysql2.host' => $medidor->host]);
        config(['database.connections.mysql2.port' => $medidor->port]);
        config(['database.connections.mysql2.database' => $medidor->database]);
        config(['database.connections.mysql2.username' => $medidor->username]);
        config(['database.connections.mysql2.password' => $medidor->password]);
        env('MYSQL2_HOST',$medidor->host);
        env('MYSQL2_DATABASE',$medidor->database);
        env('MYSQL2_USERNAME', $medidor->username);
        env('MYSQL2_PASSWORD', $medidor->password);
        try {
            \DB::purge('mysql2');
            \DB::connection('mysql2')->getPdo();
            $db = \DB::connection('mysql2');
            
            $data_area = $db->table("Area_Cliente")->first();
            $db->table("Area_Cliente")->where("ID", $data_area->ID)
                ->update(["TIPO_DE_CONTRATO_ENERGIA" =>  $request->get("contract"),
                            "PERFIL_USUARIO" => $request->get("profile")  ]);
            $this->updateLogoContador($db, $request->file("avatar"), $medidor->id);
        }
        catch (\Exception $e) {
            $data = [];
            $data["error"] = true;
            return $data;
        }
        
        $data = [];
        $data["error"] = false;
        $data["data"] = $medidor;
        return $data;
    }
    
    public function update(Request $request, $meter_id)
    {
        $medidor = EnergyMeter::find($meter_id);
        if(!$medidor)
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = [];
            return $data;
        }
        $messages = [
            'id.required' => 'ID Requerido',
            'id.numeric' => 'ID debe ser numerico',
            'name.required' => 'Debes agregar un nombre',
            'host.required' => 'Debes agregar un host',
            'database.required' => 'Debes agregar una base de datos',
            'username.required' => 'Debes agregar un usuario',
            'password.required' => 'Debes agregar una contraseña',
            'port.required' => 'Debes agregar un puerto',
            'port.numeric' => 'El puerto debe ser numerico',
            'rate.required' => 'Debes escoger una tarifa',
            'type.required' => 'Debes escoger un tipo',
            'contract.required' => 'Debes escoger un tipo de contrato',
        ];
        
        $v = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required',
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required|numeric',
            'rate' => 'required',
            'type' => 'required',
            'contract' => 'required'
        ],$messages);
        
        if ($v->fails())
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = $v->errors();
            return $data;
        }
        
        
        $medidor->count_label = $request->get("name");
        $medidor->host = $request->get("host");
        $medidor->port = $request->get("port");
        $medidor->database = $request->get("database");
        $medidor->username = $request->get("username");
        $medidor->password = $request->get("password");
        $medidor->tipo = $request->get("type");
        $medidor->tarifa = $request->get("rate");
        $medidor->production_databases = $request->get("production_databases"); #@Leo W nuevo campo para el listado de base de datos de produccion
        $medidor->save();
        
        config(['database.connections.mysql2.host' => $medidor->host]);
        config(['database.connections.mysql2.port' => $medidor->port]);
        config(['database.connections.mysql2.database' => $medidor->database]);
        config(['database.connections.mysql2.username' => $medidor->username]);
        config(['database.connections.mysql2.password' => $medidor->password]);
        env('MYSQL2_HOST',$medidor->host);
        env('MYSQL2_DATABASE',$medidor->database);
        env('MYSQL2_USERNAME', $medidor->username);
        env('MYSQL2_PASSWORD', $medidor->password);
        
        try {
            \DB::purge('mysql2');
            \DB::connection('mysql2')->getPdo();
            $db = \DB::connection('mysql2');
            
            $data_area = $db->table("Area_Cliente")->first();
            $db->table("Area_Cliente")->where("ID", $data_area->ID)
                    ->update(["TIPO_DE_CONTRATO_ENERGIA" =>  intval($request->get("contract")),
                        "PERFIL_USUARIO" => intval($request->get("profile"))  ]);
            $data_area = $db->table("Area_Cliente")->first();
            $this->updateLogoContador($db, $request->file("avatar"), $medidor->id);
        }
        catch (\Exception $e) {
            $data = [];
            $data["error"] = true;            
            return $data;
        }
        
        $data = [];
        $data["error"] = false;
        $data["data"] = $medidor;
        return $data;
    }
    
    public function delete(Request $request, $meter_id)
    {
        $medidor = EnergyMeter::find($meter_id);
        if(!$medidor)
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = [];
            return $data;
        }
        $medidor->delete();
        
        $data = [];
        $data["error"] = false;
        return $data;
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
    
    public function changeCurrentMeter(Request $request, $user_id, $meter_id)
    {
        $current_user = Auth::user();
        $meter = EnergyMeter::find($meter_id);
        $user = User::find($user_id);
        
        if($user && $meter)
        {
            CurrentCount::where("user_id", $user->id)->where("user_auth_id", $current_user->id)
                ->update(["meter_id" => $meter->id, "label_current_count" => $meter->label]);
        }
        if($request->has("return_to")){
            return redirect($request->return_to);
        }
        return back();
    }
}
