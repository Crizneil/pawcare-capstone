<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineInventory extends Model
{
    protected $table = 'vaccine_inventories';

    protected $fillable = [
        'name',
        'batch_no',
        'description',
        'stock',
        'expiry_date',
        'low_stock_threshold'
    ];
}
