<?php

namespace App\Http\Controllers;
use App\Groups;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Count;
use App\CurrentCount;
use App\EnterpriseUser;
use App\UserEnterpriseGroups;
use App\UserEnergyMeters;
use App\EnergyMeter;
use App\User;
use App\EnterpriseEnergyMeter;
use App\Enterprise;

class GroupsController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	
	public function getGroup(Groups $group)
	{
		$data = [];
		$data["error"] = false;
		$data["group"] = $group;
		return $data;
	}
	
	public function getGroups()
	{
		$groups = Groups::orderBy("nombre")->get();
		
		$data = [];
		$data["error"] = false;
		$data["groups"] = $groups;
		return $data;
	}
	
	public function storeGroup(Request $request)
	{
		$group_id = $request->get("group_id");
		$name = $request->get("name");
		$menus = $request->get("groupMenu");
		
		if($group_id == null)
		{
			$group = new Groups;            
		}
		else
		{
			$group = Groups::find($group_id);
		}
		$group->nombre = $name;
		
		if($menus)
		{
			foreach ($menus as $index => $menu)
			{
				$menus[$index] = intval($menu);
			}
			$group->opciones = serialize($menus);
		}
		$group->save();
		
		$groups = Groups::orderBy("nombre")->get();
		
		$data = [];
		$data["error"] = false;
		$data["groups"] = $groups;
		return $data;
	}
	
	public function deleteGroup(Request $request)
	{
		$group = Groups::find($request->get("group_id"));
		if($group)
		{
			$group->delete();
		}
		
		$groups = Groups::orderBy("nombre")->get();
		
		$data = [];
		$data["error"] = false;
		$data["groups"] = $groups;
		return $data;
	}
	
	public static function checkMenu($menu_id, $user_id)
	{
		$show_menu = false;
		// $request = new Request();
		// $contador = strtolower($request->get('contador'));
		$current_user = Auth::user();
		$user = User::find($user_id);
		
		if($current_user->tipo == 1){
			$show_menu = true;
			return $show_menu;
		}

		$uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
		if($uEnterprise){
			$enterprise = Enterprise::find($uEnterprise->enterprise_id);
			if($enterprise){
				$current_count = CurrentCount::where("user_id", $user->id)->where("user_auth_id", $current_user->id)->first();                
				$energy_meter = EnergyMeter::where("id", $current_count->meter_id)->first();
				if(!$current_count){
					$meters = UserEnergyMeters::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)->get();
					foreach($meters as $meter){
						$enterprise_meter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)->where("meter_id", $meter->id)->first();
						if($energy_meter){
							$energy_meter = EnergyMeter::find($meter->meter_id);
							CurrentCount::where("user_id", $user->id)->where("user_auth_id", $current_user->id)->update(["meter_id", $energy_meter->id]);
							break;
						}
					}
				}
				if($energy_meter){
					$user_menu = UserEnterpriseGroups::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)->where("meter_id", $energy_meter->id)->first();
					if($user_menu){
						$group = Groups::find($user_menu->group_id);
						if($group){
							$menus = unserialize($group->opciones);
							if(array_search($menu_id, $menus) !== false){
								$show_menu = true;
							}
						}
					}
				}
			}
		}		
		return $show_menu;
	}
	
	public static function checkContadorMenu($user_id, $menu_id, $meter_id)
	{
		$show_contador = false;        
		
		$user = User::find($user_id);
		$uEnterprise = EnterpriseUser::where("user_id", $user->id)->first();
		$enterprise = Enterprise::find($uEnterprise->enterprise_id);        
		
		if($enterprise && $user)
		{
			$meter = UserEnergyMeters::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)
						->where("meter_id", $meter_id)->first();
			
			if($meter)
			{
				$enterprise_meter = EnterpriseEnergyMeter::where("enterprise_id", $enterprise->id)
										->where("meter_id", $meter_id)->first();
				if($enterprise_meter)
				{
					$user_menu = UserEnterpriseGroups::where("user_id", $user->id)->where("enterprise_id", $enterprise->id)
									->where("meter_id", $meter_id)->first();
					if($user_menu)
					{
						$group = Groups::find($user_menu->group_id);
						if($group)
						{
							$menus = unserialize($group->opciones);
							if(array_search($menu_id, $menus) !== false)
							{
								$show_contador = true;
							}
						}
					}
				}
			}
		}
		return $show_contador;
	}
}
