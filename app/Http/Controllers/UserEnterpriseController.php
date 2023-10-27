<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use Validator;

class UserEnterpriseController extends Controller
{
    public function list(Request $request)
    {
        $columnNames = [0=>["id", true], 1 =>["name", true], 2 => ["email", true], 3 => ["edit", false], 4 => ["edit", false], 5=>["delete", false]];
        $columns = $request["columns"];
        $search = $request["search"];
        $orders = $request["order"];
        $startData = $request["start"];
        $lengthData = $request["length"];
        $dataList = User::take(1e9);
        
        if(strlen($search["value"]) > 0)
        {
            foreach($columns as $index => $column)
            {
                if($column["searchable"] == "true" && $columnNames[$index][1] && $index != 1)
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
        
        $dataRows = [];
        foreach($data as $dat)
        {
            $dataRows[] = [$dat->id, $dat->name , $dat->email, 1, 1, 1];
        }
        
        $dataList = [];
        $dataList["draw"] = $request->get("draw");
        $dataList["recordsTotal"] = $total;
        $dataList["recordsFiltered"] = $total;
        $dataList["data"] = $dataRows;
        return $dataList;
    }
    
    public function show(Request $request, $user_id)
    {
        $user = User::find($user_id);
        
        $data = [];
        $data["id"] = $user->id;
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["type"] = $user->tipo;
        
        $dataSend = [];
        $dataSend["error"] = false;
        $dataSend["data"] = $data;
        return $dataSend;
    }
    
    public function save(Request $request)
    {
        $messages = [
            'email.required' => 'Correo electronico requerido',
            'email.email' => 'Correo electronico invalido',
            'email.unique' => 'Correo electronico ya utilizado',
            'password.required' => 'Contraseña requerida',
            'name.required' => 'Debes agregar un nombre',
            'type.required' => 'Debes seleccionar un Tipo',
            'type.numeric' => 'El tipo debe ser numerico'
        ];
        
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:users',
            'type' => 'required|numeric'
        ],$messages);
        
        if ($v->fails())
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = $v->errors();
            return $data;
        }
        
        $user = new User;       
        
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->tipo = $request->get("type");
        $password = $request->get("password");
        $user->password = bcrypt($password);        
        $user->save();
        
        $data = [];
        $data["error"] = false;
        $data["data"] = $user;
        return $data;
    }
    
    public function update(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if(!$user)
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = [];
            return $data;
        }
        $messages = [
            'id.required' => 'ID Requerido',
            'id.numeric' => 'ID debe ser numerico',
            'email.required' => 'Correo electronico requerido',
            'email.email' => 'Correo electronico invalido',
            'email.unique' => 'Correo electronico ya utilizado',
            'name.required' => 'Debes agregar un nombre',
            'type.required' => 'Debes seleccionar un Tipo',
            'type.numeric' => 'El tipo debe ser numerico'
        ];
        
        $v = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique("users")->ignore($user->id)],
            'type' => 'required|numeric'
        ],$messages);
        
        if ($v->fails())
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = $v->errors();
            return $data;
        }
        
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->tipo = $request->get("type");
        $password = $request->get("password");
        if(strlen($password) > 0)
        {
            $user->password = bcrypt($password);
        }
        $user->save();
        
        $data = [];
        $data["error"] = false;
        $data["data"] = $user;
        return $data;
    }
    
    public function delete(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if(!$analizador)
        {
            $data = [];
            $data["error"] = true;
            $data["messages"] = [];
            return $data;
        }
        $user->delete();
        
        $data = [];
        $data["error"] = false;
        return $data;
    }
    
}
