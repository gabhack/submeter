<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Mail\CambioContrasena;
use Mail;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * [Rogelio R - Workana] Se agrega el campo "lock_status", que es el que habilitará la opción de bloquear una cuenta.
     * los valores esperados para este campo son 'UNLOCKED', 'LOCKOUT' y 'LOCKED'
     * 
     * [Rogelio R - Workana] Se agrega el campo "phone_number" para el almacenamiento del numero telefonico al cual se enviaran SMS
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'lock_status', 'phone_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'enterprise_name',
    ];
    
    protected $appends = ['energy_meters', 'current_count'];

    public function _perfil()
    {
      return $this->hasOne(Perfil::class);
    }

    public function _count()
    {
        return $this->hasMany(Count::class);
    }

    public function _notification()
    {
        return $this->hasMany(Notification::class);
    }
    
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new CambioContrasena($token));
    }
    
    public function group()
    {
        return $this->hasOne("App\Groups", "id", "group_id");
    }
    
    public function getEnergyMetersAttribute()
    {
        $energy_meters = [];
        $enterprise = EnterpriseUser::where("user_id", $this->id)->first();
        if($enterprise)
        {
            $user_energy = UserEnergyMeters::where("user_id", $this->id)->get();
            
            foreach($user_energy as $uEnergy)
            {
                $enterprise_energy = EnterpriseEnergyMeter::where("meter_id", $uEnergy->meter_id)
                                        ->where("enterprise_id", $enterprise->enterprise_id)->first();
                if($enterprise_energy)
                {
                    $energy_meters[] = $uEnergy->meter_id;
                }
            }            
            $energy_meters = EnergyMeter::whereIn('id', $energy_meters)->get();
        }
        return $energy_meters;
    }
    
    public function getCurrentCountAttribute()
    {
        $current_user = Auth::user();
        $current_count = new \stdClass();
        $current_count->id = -1;
        $cCount = CurrentCount::where("user_auth_id", $current_user->id)->where("user_id", $this->id)->first();
        if($cCount)
        {
            $current_count = $cCount;
        }
        return $current_count;
    }
    
    public function getEnterpriseNameAttribute()
    {
        $enterprise_name = "";
        $uEnterprise = EnterpriseUser::where("user_id", $this->id)->first();
        if($uEnterprise)
        {
            $enterprise = Enterprise::where("id", $uEnterprise->enterprise_id)->first();
            if($enterprise)
            {
                $enterprise_name = $enterprise->name;
            }
        }
        return $enterprise_name;
    }
}
