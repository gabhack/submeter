<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionConfiguration extends Model
{

    # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se cargen sus configuraciones
    public function scopeByUser($query)
    {
        if(auth()->user()->tipo == 2){
            $entObj = EnterpriseUser::where("user_id", auth()->user()->id)->first();
            $enterprise_id = 0;
            if($entObj) $enterprise_id = $entObj->enterprise_id;
            return $query->select('production_configurations.*')->where('enterprise_id', '=', $enterprise_id);
        }
        return $query;
    }

    public function production_fields()
    {
        return $this->hasMany("App\ProductionField", "configuration_id", "id")->orderBy("id", "asc");
    }
    
    public function production_group_fields()
    {
        return $this->hasMany("App\ProductionGroupField", "configuration_id", "id")->orderBy("id", "asc");
    }
}
