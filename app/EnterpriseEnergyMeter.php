<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnterpriseEnergyMeter extends Model
{
    public function meter()
    {
        return $this->hasOne("App\EnergyMeter", "id", "meter_id");
    }
}
