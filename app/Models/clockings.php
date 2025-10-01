<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clockings extends Model
{
    //

    protected $guarded = [];


    public function getEmployee()
    {
        return $this->belongsTo(Employees::class, 'employee_id', 'id');
    }

}
