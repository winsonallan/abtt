<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'product';
    protected $primaryKey = 'product_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
