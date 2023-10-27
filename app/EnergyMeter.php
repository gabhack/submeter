<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnergyMeter extends Model
{
    # @Leo W* nuevo campo para guardar la lista de BD de produccion 
    protected $casts = [
        'production_databases' => 'array',
    ];

    # @Leo W* crear scope para en caso de que el usuario no sea admin, solo se cargue los energy metters de su empresa
    public function scopeByUser($query)
    {
        if(auth()->user()->tipo == 2){
            $entObj = EnterpriseUser::where("user_id", auth()->user()->id)->first();
            $enterprise_id = 0;
            if($entObj) $enterprise_id = $entObj->enterprise_id;
            return $query->select('*')->whereIn('id',function($subq) use($enterprise_id){
                $subq->select('meter_id')->from('enterprise_energy_meters')->where('enterprise_id','=',$enterprise_id);
            });
        }
        return $query;
    }
    
    public function _analizador()
    {
        return $this->hasMany("App\Analizador", "count_id", "id");
    }
    
    public function analyzer_meters()
    {
        return $this->hasMany("App\AnalyzerMeter", "meter_id", "id");
    }

    public function find_production_connection($id)
    {
        foreach ($this->production_databases as $conn) 
        {
            if($conn['id'] == $id) return $conn;
        }
        return null;
    }

    public function getProductionDatabases()
    {
        if($this->production_databases) return $this->production_databases;
        return [];
    }
	
	public function analyzers(){
        return $this->belongsToMany(Analizador::class, "analyzer_meters", "meter_id", "analyzer_id");
    }
}
