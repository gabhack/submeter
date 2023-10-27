<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CurrentCount;
use App\EnergyMeter;
use App\UserEnergyMeters;
use App\EnterpriseUser;
use App\Enterprise;
use App\EnterpriseEnergyMeter;
use Session;
use Auth;

class ContadorController extends Controller
{
    public static function getCurrrentController($dataRequest)
    {
        $user = $dataRequest["user"];        
        $userAuth = Auth::user();       
        
        $contador2 = null;
        $current_count = CurrentCount::where("user_id", $user->id)->where("user_auth_id", $userAuth->id)->first();
        if($current_count)
        {
            $contador2 = EnergyMeter::find($current_count->meter_id);
            if(!$contador2)
            {
                $uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
                
                if($uEnterprise)
                {
                    $enterprise = Enterprise::find($uEnterprise->enterprise_id);
                    if($uEnterprise && $enterprise)
                    {
                        $uMeters = UserEnergyMeters::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)->get();                        
                        foreach($uMeters as $uMeter)
                        {
                            $eMeter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)->where("meter_id", $uMeter->meter_id)->first();
                            if($eMeter)
                            {
                                $contador2 = EnergyMeter::find($uMeter->meter_id);
                                break;
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
            if($uEnterprise)
            {
                $enterprise = Enterprise::find($uEnterprise->enterprise_id);
                if($uEnterprise && $enterprise)
                {
                    $uMeters = UserEnergyMeters::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)->get();
                    foreach($uMeters as $uMeter)
                    {
                        $eMeter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)->where("meter_id", $uMeter->meter_id)->first();
                        if($eMeter)
                        {
                            $contador2 = EnergyMeter::find($uMeter->meter_id);
                            break;
                        }
                    }
                }
            }
        }
        
        if($contador2)
        {
            $current_count = CurrentCount::where("user_id", $user->id)->where("user_auth_id", $userAuth->id)->first();
            if(!$current_count)
            {
                $current_count = new CurrentCount;
                $current_count->user_id = $user->id;
                $current_count->user_auth_id = $userAuth->id;                
            }
            $current_count->label_current_count = $contador2->count_label;
            $current_count->meter_id = $contador2->id;
            $current_count->save();
        }
        if(!$contador2)
        {
            $contador2 = new \stdClass();
            $contador2->tipo = 4;
            $contador2->tarifa = 4;
            $contador2->count_label = "";
            $contador2->host = "";
            $contador2->port = "";
            $contador2->database = "";
            $contador2->username = "";
            $contador2->password = "";
        }
        return $contador2;
    }
}
