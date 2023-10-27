<?php

namespace App;

use App\Helpers\DataHelper;
use Illuminate\Database\Eloquent\Model;

class StatisticConfiguration extends Model
{
    protected $casts = [
        'fields' => 'array',
    ];

    public function scopeByUser($query)
    {
        if(auth()->user()->tipo == 2){
            $entObj = EnterpriseUser::where("user_id", auth()->user()->id)->first();
            $enterprise_id = 0;
            if($entObj) $enterprise_id = $entObj->enterprise_id;
            return $query->select('statistic_configurations.*')->where('enterprise_id', '=', $enterprise_id);
        }
        return $query;
    }

    public function scopeFilter($query,$data)
    {
        if(array_key_exists('emp_id',$data)) $query->where('enterprise_id','=',$data['emp_id']);
        if(array_key_exists('type',$data)) $query->where('type','=',$data['type']);
        
        return $query;
    }

    public function getFields()
    {
        if($this->fields) return $this->fields;
        return array();
    }
    
}
