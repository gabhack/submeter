<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Mail\MailTest;
use Illuminate\Support\Facades\Mail;
use App\Perfil;
use Auth;
use File;
use PDF;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('id',3)->get()->first();        
        $array_coste_activa = array();
        $coste_activa = 0;
        $array_coste_reactiva = array();
        $coste_reactiva = 0;
        $array_potencia_contratada = array();
        $potencia_contratada = 0;
        $array_exceso_potencia = array();
        $exceso_potencia = 0;
        $array_impuesto = array();
        $impuesto = 0;
        $array_equipo = array();
        $equipo = 0;
        // variables
        $E_Activa = array();
        $MES = array();
        $precio_energia = array();
        $E_Reactiva = array();
        $potencia_demandada = array();

        config(['database.connections.mysql2.host' => '172.30.126.162']);
        config(['database.connections.mysql2.port' => '3306']);
        config(['database.connections.mysql2.database' => 'ACOR-V1_PM1_(Cons_FCA)_(Tarifa-6.2_ID-9']);
        config(['database.connections.mysql2.username' => 'user201']);
        config(['database.connections.mysql2.password' => 'AIQCvMDns03rFX3L']);
        env('MYSQL2_HOST','172.30.126.162');
        env('MYSQL2_DATABASE','ACOR-V1_PM1_(Cons_FCA)_(Tarifa-6.2_ID-9');
        env('MYSQL2_USERNAME', 'user201');
        env('MYSQL2_PASSWORD','AIQCvMDns03rFX3L');
        $db = \DB::connection('mysql2');        

        $interval = 4;

        $titulo = 'Simulacion de Factura';

        switch ($interval) {
            case '1':
                $date_from = \Carbon\Carbon::yesterday()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Ayer';                
            break;

            case '3':
                $date_from = \Carbon\Carbon::now()->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Actual';
            break;

            case '4':
                $date_from = \Carbon\Carbon::now()->subWeeks(1)->startOfWeek()->toDateString();
                $date_to = \Carbon\Carbon::now()->subWeeks(1)->endOfWeek()->toDateString();
                $label_intervalo = 'Semana Anterior';
            break;

            case '5':
                $date_from = \Carbon\Carbon::now()->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Actual';
            break;

            case '6':
                $date_from = \Carbon\Carbon::now()->subMonths(1)->startOfMonth()->toDateString();
                $date_to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth()->toDateString();
                $label_intervalo = 'Mes Anterior';
            break;
            
            case '7':
                $date_from = \Carbon\Carbon::now()->subMonths(3)->toDateString();
                $date_to = \Carbon\Carbon::now()->toDateString();
                $label_intervalo = 'Último Trimestre';
            break;

            case '8':
                $date_from = \Carbon\Carbon::now()->subYears(1)->startOfYear()->toDateString();
                $date_to = \Carbon\Carbon::now()->subYears(1)->endOfYear()->toDateString();
                $label_intervalo = 'Último Año';
            break;

            default:
                $date_from = \Carbon\Carbon::now()->toDateString();
                $date_to = $date_from;
                $label_intervalo = 'Hoy';
            break;
        }
        for ($i=1; $i < 7 ; $i++) { 
            $periodos2[] = 'P'.$i;            
        }

        if($db->table('information_schema.columns')->select(\DB::raw("column_name"))->whereRaw("table_name = 'Potencia_Contratada_Optima' AND column_name = 'Potencia_contratada'")->first())
        {                
            if($interval < 3)
            {
                $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,`Potencia_contratada` p_optima"))->orderBy('Periodo')->get();                
            }else{
                $potencia_optima = $db->table('Potencia_Contratada_Optima')->select(\DB::raw("Periodo eje,MAX(`Potencia_contratada`) p_optima"))->orderBy('Periodo')->get();
            }
        }else{
            $potencia_optima[0]['p_optima'] =0;
            $potencia_optima[1]['p_optima'] =0;
            $potencia_optima[2]['p_optima'] =0;
            $potencia_optima[3]['p_optima'] =0;
            $potencia_optima[4]['p_optima'] =0;
            $potencia_optima[5]['p_optima'] =0;
        }

        $MES = $db->table('Datos_Contador')->select(\DB::raw("MONTH(date) as MES"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy(\DB::raw('MONTH(date)'))->get()->toArray();

        // $MES = \DB::select("SELECT MONTH(".$contador.".datos_contador.date) as MES FROM ".$contador.".datos_contador WHERE ".$contador.".datos_contador.date >= '".$date_from."' AND ".$contador.".datos_contador.date <= '".$date_to."' GROUP BY MONTH(".$contador.".datos_contador.date)");

        // COSTE DE LA ENERGÍA ACTIVA
        $precio_energia = $db->table('Precio_Energia')->select('precio')->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray();

         // \DB::select("SELECT precio FROM ".$contador.".`precio_energia` WHERE date_start <= '".$date_from."' AND date_end >= '".$date_to."'");

        //CANTIDAD DE POTECIA CONSUMIDA EN KWH
        $potencia_demandada = $db->table('Potencia_Demandada_Contratada')->select(\DB::raw("Periodo, SUM(`Potencia Contratada (kW)`) potencia_demandada"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

        // $potencia_demandada =\DB::select("SELECT Periodo, SUM(`Potencia Demandada (kW)`) potencia_demandada FROM ".$contador.".`potencia_demandada_contratada` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Periodo");

        // COSTE DE LA POTENCIA CONTRATADA
        $precio_potencia = $db->table('Precio_Potencia')->select(\DB::raw("Periodo, Precio precio_potencia"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->groupBy('Periodo')->get()->toArray();        

        // $precio_potencia = \DB::select("SELECT Periodo, `Coste Potencia Contratada (€)` precio_potencia FROM ".$contador.".`coste_potencia_contratada` WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Periodo");

        // EXCESOS DE POTENCIA
        $exceso_potencia = $db->table('Coste_Exceso_Potencia')->select(\DB::raw("Periodo, SUM(`Coste Exceso Potencia (€)`) excesos"))->where('date','>=',$date_from)->where('date','<=',$date_to)->groupBy('Periodo')->get()->toArray();

        // \DB::select("SELECT Periodo, SUM(`Coste Exceso Potencia (€)`) excesos FROM ".$contador.".coste_exceso_potencia WHERE date >= '".$date_from."' AND date <= '".$date_to."' GROUP BY Periodo");

        foreach ($MES as $mes) {
            foreach ($periodos2 as $P) {
                $E_Activa[] = $db->table('Consumo_Energia_Activa')->select(\DB::raw("SUM(`Energia Activa (kWh)`) as Activa"))->join('Tarifa',"Consumo_Energia_Activa.time",">=",\DB::raw("Tarifa.hora_start AND Tarifa.Mes = ".$mes->MES." AND Consumo_Energia_Activa.time < Tarifa.hora_end"))->where("Consumo_Energia_Activa.date", '>=',$date_from)->where("Consumo_Energia_Activa.date", '<=',$date_to)->where("Tarifa.Periodo",$P)->where(\DB::raw('MONTH(Consumo_Energia_Activa.date)'),$mes->MES)->get()->toArray();

                // \DB::select("SELECT SUM(".$contador.".consumo_energia_activa.`Energia Activa (kWh)`) as Activa FROM ".$contador.".consumo_energia_activa INNER JOIN ".$contador.".tarifa ON ".$contador.".tarifa.Mes IN (".$mes->MES.") AND ".$contador.".consumo_energia_activa.time >= ".$contador.".tarifa.hora_start AND ".$contador.".consumo_energia_activa.time < ".$contador.".tarifa.hora_end WHERE ".$contador.".consumo_energia_activa.date >= '".$date_from."' AND ".$contador.".consumo_energia_activa.date <= '".$date_to."' AND ".$contador.".tarifa.Periodo IN ('".$P."') AND MONTH(".$contador.".consumo_energia_activa.date) = ".$mes->MES);

                // NO SE ENCUENTRA REGISTRADO EN LA FACTURA
                // $E_Reactiva[] = \DB::select("SELECT SUM(".$contador.".datos_contador.`EAct imp(kWh)`)+SUM(".$contador.".datos_contador.`EAct exp(kWh)`) as Activa FROM ".$contador.".datos_contador INNER JOIN ".$contador.".tarifa ON ".$contador.".tarifa.Mes IN (".$mes->MES.") AND ".$contador.".datos_contador.time >= ".$contador.".tarifa.hora_start AND ".$contador.".datos_contador.time < ".$contador.".tarifa.hora_end WHERE ".$contador.".datos_contador.date >= '".$date_from."' AND ".$contador.".datos_contador.date <= '".$date_to."' AND ".$contador.".tarifa.Periodo IN ('".$P."') AND MONTH(".$contador.".datos_contador.date) = ".$mes->MES);
            }
        }
        $aux = array();
        $i = 0;
        $total1 = 0;
        $total2 = 0;
        $total3 = 0;

        if(!empty($E_Activa))
        {
            foreach ($E_Activa as $val) {
                // PARTE DE TERMINO ENERGÍA ACTIVA
                $totales_parciales_energiaAct[] = floatval($val[0]->Activa)*$precio_energia[$i]->precio;
                $total1 = $total1 + $totales_parciales_energiaAct[$i];

                // PARTE DE ENERGÍA REACTIVA

                // PARTE DE TÉRMINO DE POTENCIA
                $totales_parciales_potencia[] = floatval($potencia_demandada[$i]->potencia_demandada)*floatval($precio_potencia[$i]->precio_potencia);
                $total2 = $total2 + $totales_parciales_potencia[$i];

                // TOTAL DE EXCESOS
                $total3 = $total3 + floatval($exceso_potencia[$i]->excesos);

                $i++;
            }
        }else{
            $totales_parciales_energiaAct[] = 0;
            $total1= 0;
            $totales_parciales_potencia[]=0;
            $total2=0;
            $total3=0;
        }

        $sumatoria = $total1 + $total2 + $total3;
        $impuesto = $sumatoria*0.0511269632;

        $equipo = ($db->table('Alquiler_Equipo_Medida')->select(\DB::raw("Alquiler_Equipo_Medida valor"))->where('date_start','<=',$date_from)->where('date_end','>=',$date_to)->get()->toArray());
            // \DB::select("SELECT Alquiler_Equipo_Medida valor FROM ".$contador.".alquiler_equipo_medida WHERE date_start <= '".$date_from."' AND date_end >= '".$date_to."'")[0]->valor;
        // dd($equipo);
        if(!empty($equipo))
        {
            foreach ($equipo as $value) {
                $IVA = ($sumatoria + $impuesto + floatval($value->valor))*0.21;            
            }            
        }else{
            $IVA = ($sumatoria + $impuesto)*0.21;
        }
        // dd($IVA);
        
        $hoy = \Carbon\Carbon::now();
        //dd(request()->session()->all());
        $cont = $contador;
        \DB::disconnect('mysql2');

        if(3 != 0)
        {
            $user = User::where('id',3)->get()->first();
            if(Auth::user()->tipo != 1)
                $ctrl = 0;
            else
                $ctrl = 1; 

            if(!is_null($user->_perfil))
                $image = $user->_perfil->avatar;
            else{
                $image = "images/avatar.png";
            }

            $pdf = \PDF::loadView('simulacion_facturas.simulacion_facturas_pdf',compact('user','titulo','id','precio_potencia','precio_energia','E_Activa', 'potencia_demandada', 'exceso_potencia', 'impuesto', 'equipo','hoy','date_from','date_to','cont','total1','total2','total3','IVA','sumatoria','label_intervalo','ctrl','image','potencia_optima'));
            $pdf->setPaper("A4", "portrait");
            return $pdf->download("Simulacion_Facturas.pdf");     
        }

    }
}
