<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se cargue su empresa
    public function scopeByUser($query)
    {
        if(auth()->user()->tipo == 2){
            $entObj = EnterpriseUser::where("user_id", auth()->user()->id)->first();
            $enterprise_id = 0;
            if($entObj) $enterprise_id = $entObj->enterprise_id;
            return $query->select('*')->where('id', '=', $enterprise_id);
        }
        return $query;
    }

    public function enterprise_meters()
    {
        return $this->hasMany("App\EnterpriseEnergyMeter", "enterprise_id", "id");
    }
    
    public function enterprise_analyzers()
    {
        return $this->hasMany("App\EnterpriseAnalyzer", "enterprise_id", "id");
    }
    
    public function enterprise_users()
    {
        return $this->hasMany("App\EnterpriseUser", "enterprise_id", "id");
    }
}
