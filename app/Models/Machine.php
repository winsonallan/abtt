<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    //
    protected $table = 'machine';
    protected $primaryKey = 'machine_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
