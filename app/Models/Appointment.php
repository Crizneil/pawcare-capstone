<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This "unlocks" the fields so the Controller can save them.
     */
    protected $fillable = [
        'user_id',
        'pet_name',
        'species',
        'appointment_date',
        'appointment_time',
        'service_type',
        'status',
        'notes',
    ];

    /**
     * Relationship: An appointment belongs to a User (Owner).
     * This allows you to do $appointment->user->name in your table.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
        'name' => 'Guest/Walk-in', // This prevents errors when calling $apt->user->name
        'phone' => 'N/A'
    ]);
    }
}
