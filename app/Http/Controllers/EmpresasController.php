<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enterprise;
use App\EnterpriseEnergyMeter;
use App\EnterpriseAnalyzer;
use App\EnterpriseUser;
use App\UserEnergyMeters;
use App\UserEnterpriseGroups;
use App\UserAnalyzers;
use App\EnterpriseAnalyzerGroups;
use Auth;
use App\User;

class EmpresasController extends Controller
{
    public function list()
    {
        $empresas = Enterprise::all();
        $user = Auth::user();
        $tipo_count = null;
        
        return view("empresa.list", compact("empresas", "tipo_count" ,"user"));
    }
    
    public function create()
    {
        $user = Auth::user();
        $tipo_count = null;
        
        return view("empresa.create", compact("tipo_count" ,"user"));
    }
    
    public function edit($enterprise_id)
    {
        $user = Auth::user();
        $tipo_count = null;
        
        $empresa = Enterprise::find($enterprise_id);
        
        foreach($empresa->enterprise_users as $enterprise_user)
        {
            $enterprise_user->user->counts = UserEnergyMeters::where("enterprise_id", $enterprise_id)
                                                ->where("user_id", $enterprise_user->user->id)->get();
            $enterprise_user->user->analyzers = UserAnalyzers::where("enterprise_id", $enterprise_id)
                                                ->where("user_id", $enterprise_user->user->id)->get();
            $enterprise_user->user->groups = UserEnterpriseGroups::where("enterprise_id", $enterprise_id)
                                                ->where("user_id", $enterprise_user->user->id)->get();
        }
        
        $empresa->groups_analyzer = EnterpriseAnalyzerGroups::where("enterprise_id", $enterprise_id)->get();
        
        
        return view("empresa.edit", compact("tipo_count" ,"user", "empresa"));
    }
    
    public function save(Request $request)
    {
        $enterprise = new Enterprise;
        $enterprise->name = $request->get("name");
        $enterprise->save();
        
        $this->saveEnterpriseDetails($enterprise, $request);
        return redirect()->route('enterprise.index')->with('message.enterprise', 'Empresa Guardada');
    }
    
    public function update(Request $request, $enterprise_id)
    {
        $enterprise = Enterprise::find($enterprise_id);
        $enterprise->name = $request->get("name");
        $enterprise->save();
        
        $this->saveEnterpriseDetails($enterprise, $request);
        return redirect()->route('enterprise.index')->with('message.enterprise', 'Empresa Actualizada');
    }
    
    public function delete(Request $request, $enterprise_id)
    {
        $enterprise = Enterprise::find($enterprise_id);
        $enterprise->delete();
        
        $data = [];
        $data["error"] = false;
        return $data;
    }
    
    public function getUsers(Request $request, $enterprise_id)
    {
        $enterprise = Enterprise::find($enterprise_id);
        $dataUsers = [];
        if($enterprise)
        {
            $usersEnterprise = EnterpriseUser::where("enterprise_id", $enterprise_id)->get();
            foreach($usersEnterprise as $uEnterprise)
            {
                if($uEnterprise->user)
                {
                    $user = [];
                    $user["id"] = $uEnterprise->user->id;
                    $user["name"] = $uEnterprise->user->name;
                    $user["email"] = $uEnterprise->user->email;
                    $dataUsers[] = $user;
                }
            }
        }
        
        $data = [];
        $data["data"] = $dataUsers;
        $data["error"] = false;
        return $data;
    }
    
    private function saveEnterpriseDetails($enterprise, $request)
    {
        EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)
                        ->update(["updated" => 0]);
        
        $enterpriseMeters = $request->get("meters", []);
        foreach($enterpriseMeters as $eMeter)
        {
            $meter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)
                            ->where("meter_id", $eMeter)->first();
            
            if(!$meter)
            {
                $meter = new EnterpriseEnergyMeter;
                $meter->enterprise_id = $enterprise->id;
                $meter->meter_id = $eMeter;
            }
            //
            $meter->updated = 1;
            $meter->save();
        }
        
        EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)
                ->where("updated", 0)
                ->delete();
        
        EnterpriseAnalyzer::where("enterprise_id", $enterprise->id)
                ->update(["updated" => 0]);
        
        $enterpriseAnalyzers = $request->get("analyzers", []);
        
        foreach($enterpriseAnalyzers as $eAnalyzer)
        {
            $analyzer = EnterpriseAnalyzer::where("enterprise_id", $enterprise->id)
                                ->where("analyzer_id", $eAnalyzer)->first();
            if(!$analyzer)
            {
                $analyzer = new EnterpriseAnalyzer;
                $analyzer->enterprise_id = $enterprise->id;
                $analyzer->analyzer_id = $eAnalyzer;
            }
            $analyzer->updated = 1;
            $analyzer->save();
        }
        
        EnterpriseAnalyzer::where("enterprise_id", $enterprise->id)
                ->where("updated", 0)
                ->delete();
        
        EnterpriseUser::where("enterprise_id", $enterprise->id)
                ->update(["updated" => 0]);
        
        $enterpriseUsers = $request->get("users", []);
        foreach($enterpriseUsers as $eUser)
        {
            $user = EnterpriseUser::where("enterprise_id", $enterprise->id)
                        ->where("user_id", $eUser)->first();
            
            if(!$user)
            {
                $user = new EnterpriseUser;
                $user->enterprise_id = $enterprise->id;
                $user->user_id = $eUser;
            }
            $user->updated = 1;
            $user->save();
        }
        
        EnterpriseUser::where("enterprise_id", $enterprise->id)
                ->where("updated", 0)
                ->delete();
        
        $userEnergy = $request->get("userEnergy", []);
        
        foreach ($userEnergy as $idxUser => $aEnergy)
        {
            UserEnergyMeters::where("enterprise_id", $enterprise->id)
                    ->where("user_id", $idxUser)
                    ->update(["updated" => 0]);
            
            foreach($aEnergy as $uEnergy)
            {
                $user = UserEnergyMeters::where("enterprise_id", $enterprise->id)
                                ->where("user_id", $idxUser)
                                ->where("meter_id", $uEnergy)
                                ->first();
                if(!$user)
                {
                    $user = new UserEnergyMeters;
                    $user->enterprise_id = $enterprise->id;
                    $user->user_id = $idxUser;
                    $user->meter_id = $uEnergy;
                }
                $user->updated = 1;
                $user->save();
            }
            
            UserEnergyMeters::where("enterprise_id", $enterprise->id)
                    ->where("user_id", $idxUser)
                    ->where("updated", 0)
                    ->delete();
        }
        
        
        $userAnalyzer = $request->get("userAnalyzer", []);        
        
        foreach ($userAnalyzer as $idxUser => $aAnalyzer)
        {
            UserAnalyzers::where("enterprise_id", $enterprise->id)
                    ->where("user_id", $idxUser)
                    ->update(["updated" => 0]);
            
            foreach($aAnalyzer as $uAnalyzer)
            {
                $user = UserAnalyzers::where("enterprise_id", $enterprise->id)
                                ->where("user_id", $idxUser)
                                ->where("analyzer_id", $uAnalyzer)
                                ->first();
                if(!$user)
                {
                    $user = new UserAnalyzers;
                    $user->enterprise_id = $enterprise->id;
                    $user->user_id = $idxUser;
                    $user->analyzer_id = $uAnalyzer;
                }
                $user->updated = 1;                
                $user->save();
            }
            
            UserAnalyzers::where("enterprise_id", $enterprise->id)
                    ->where("user_id", $idxUser)
                    ->where("updated", 0)
                    ->delete();
        }
        
        
        $userGroups = $request->get("userGroup", []);
        
        foreach($userGroups as $idxUser => $data)
        {
           UserEnterpriseGroups::where("enterprise_id", $enterprise->id)
                    ->where("user_id", $idxUser)
                    ->update(["updated" => 0]);
           
           foreach($data as $idxMeter => $idxGroup)
           {
               $group = UserEnterpriseGroups::where("enterprise_id", $enterprise->id)
                                ->where("user_id", $idxUser)
                                ->where("meter_id", $idxMeter)
                                ->where("group_id", $idxGroup)
                                ->first();
               if(!$group)
               {
                   $group = new UserEnterpriseGroups;
                   $group->enterprise_id = $enterprise->id;
                   $group->user_id = $idxUser;
                   $group->meter_id = $idxMeter;
                   $group->group_id = $idxGroup;
               }
               $group->updated = 1;
               $group->save();
           }
           
           UserEnterpriseGroups::where("enterprise_id", $enterprise->id)
                   ->where("user_id", $idxUser)
                   ->where("updated", 0)
                   ->delete();
        }
        
        $analyzerGroups = $request->get("analyzerGroups", []);
        EnterpriseAnalyzerGroups::where("enterprise_id", $enterprise->id)
                            ->update(["updated" => 0]);
        
        foreach($analyzerGroups as $analyzerGroup)
        {
            $aGroup = EnterpriseAnalyzerGroups::where("enterprise_id", $enterprise->id)
                            ->where("analyzer_group_id", $analyzerGroup)
                            ->first();
            if(!$aGroup)
            {
                $aGroup = new EnterpriseAnalyzerGroups;
                $aGroup->enterprise_id = $enterprise->id;
                $aGroup->analyzer_group_id = $analyzerGroup;
            }
            $aGroup->updated = 1;
            $aGroup->save();
        }
        
        EnterpriseAnalyzerGroups::where("enterprise_id", $enterprise->id)
                            ->where("updated", 0)
                            ->delete();
    }
}
