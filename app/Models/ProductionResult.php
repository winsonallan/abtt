<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionResult extends Model
{
    //
    protected $table = 'production_result';
    public $timestamps = false;
    protected $fillable = [
        'wo_number',
        'actual_start',
        'actual_finish',
        'runtime_minutes',
        'good_qty',
        'reject_qty',
        'achievement',
    ];

    protected $casts = [
        'actual_start' => 'datetime',
        'actual_finish' => 'datetime',
        'runtime_minutes' => 'integer',
        'good_qty' => 'integer',
        'reject_qty' => 'integer',
        'achievement' => 'float',
    ];
}
