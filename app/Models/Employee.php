<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $table = 'employee';
    protected $primaryKey = 'employee_no';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
