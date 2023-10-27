<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class NewConnectionsController extends Controller
{
         public function saveConnection(Request $request)
        {
              $response = \DB::table('energy_meters')->insert([
                  [
                        'count_label'  => $request->count_label, 
                        'host'         => $request->host,
                        'port'         => $request->port, 
                        'database'     => $request->database,
                        'username'     => $request->username, 
                        'password'     => $request->password
                  ]
                    
                ]);

            $data = [];
             if ($response) 
             {
                    $request->session()->flash('message.production', 'Conexion guardada con exito');
                    return redirect()->route('production.list');
             }else{
                    $data["error"] = true;
                    return $data;
             }
        }
}
