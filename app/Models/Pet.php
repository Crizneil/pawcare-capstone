<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'birthdate',
        'registry_date',
        'unique_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vaccinations()
    {
        return $this->hasMany(PetVaccination::class);
    }
}
