<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccineInventory extends Model
{
    use SoftDeletes;
    protected $table = 'vaccine_inventories';

    protected $fillable = [
        'name',
        'batch_no',
        'received_date',
        'description',
        'stock',
        'expiry_date',
        'low_stock_threshold'
    ];
}
