<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetVaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'vaccine_name',
        'administered_at',
        'next_due_at',
        'administered_by',
        'price',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
