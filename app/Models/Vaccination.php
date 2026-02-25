<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
        protected $fillable = [
        'pet_id',
        'staff_id',
        'vaccine_name',
        'date_administered',
        'next_due_date',
        'remarks',
        'status',
        'batch_no',
        'remarks',
        'admin_id',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
