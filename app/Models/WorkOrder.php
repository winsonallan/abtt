<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    //
    protected $table = 'work_order';
    protected $primaryKey = 'wo_number';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'target_qty' => 'integer',
        'plan_start' => 'datetime',
        'plan_finish' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_code', 'machine_code');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_no', 'employee_no');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ProductionResult::class, 'wo_number', 'wo_number');
    }
}
