<?php

namespace App;

use App\Helpers\DataHelper;
use Illuminate\Database\Eloquent\Model;

class ManualConfiguration extends Model
{
    protected $casts = [
        'data' => 'array','meta'=>'array'
    ];

    public function scopeByUser($query)
    {
        if(auth()->user()->tipo == 2){
            $entObj = EnterpriseUser::where("user_id", auth()->user()->id)->first();
            $enterprise_id = 0;
            if($entObj) $enterprise_id = $entObj->enterprise_id;
            return $query->select('manual_configurations.*')->where('enterprise_id', '=', $enterprise_id);
        }
        return $query;
    }

    public function getData()
    {
        if($this->data) return $this->data;
        return array();
    }
    
}
