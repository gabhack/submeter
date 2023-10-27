<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionField extends Model
{
    public function operation()
    {
        return $this->hasOne("App\FieldOperation", "id", "operation_id");
    }
    
    public function operands()
    {
        return $this->hasMany("App\ProductionFieldOperand", "production_field_id", "id");
    }
}
