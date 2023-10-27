<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudRegistro;
use App\Http\Requests\UsuarioRegistradoRequest;
use App\User;
use App\Perfil;
use Session;
use Auth;

class UsuarioRegistradoController extends Controller
{
    function usuarioRegistrado()
    {
        return view('User.usuario_registrado');
    }

    function saveRegistro(UsuarioRegistradoRequest $request)
    {


        $user = User::where('email', $request->email)->first();

        if($user){

            if (Hash::check($request->codigo, $user->password)) {
                
                $user->password = Hash::make($request->password);
                $user->save();

                $perfil = new Perfil();

                $perfil->nombre = $request->name;
                $perfil->apellido = $request->apellido;
                $perfil->direccion = $request->direccion;
                $perfil->user_id = $user->id;
                $perfil->save();

                return redirect()->to('/login');
            }else{

                Session::flash('message-error', 'El código no es correcto. Por favor, rectificalo e intenta de nuevo!');
                return \Redirect::back();
            }
        }else{

            Session::flash('message-error', 'El usuario con el correo no existe!');
            return \Redirect::back();
        }
    }

    function solicitarRegistro(Request $request)
    {
        return view('auth.solicitud_registro');
    }

    function enviarSolicitudRegistro(Request $request)    
    {
        $nombre = $request->nombre_prospecto;
        $apellido = $request->apellido_prospecto;
        $empresa = $request->empresa_prospecto;
        $correo = $request->correo_prospecto;
        $telefono = $request->telefono_prospecto;
        switch ($request->tipo_monitorizacion) {
            case 1:
                $tipo_monitorizacion = "Contadores Eléctricos";
                break;

            case 2:
                $tipo_monitorizacion = "Contadores de Gas";
                break;

            case 3:
                $tipo_monitorizacion = "Analizadores Eléctricos";
                break;

            case 4:
                $tipo_monitorizacion = "Producción Scada | ERP";
                break;

            case 5:
                $tipo_monitorizacion = "Otros";
                break;
            
            default:
                $tipo_monitorizacion = "Sin tipo";
                break;
        }
        
        Mail::to('info@3seficiencia.com', 'Submeter 4.0')
                ->send(new SolicitudRegistro($nombre,$apellido,$empresa,$correo,$telefono,$tipo_monitorizacion));

        Session::flash('message', 'Se ha enviado la solicitud con éxito.');
        return \Redirect::back();
    }
}
