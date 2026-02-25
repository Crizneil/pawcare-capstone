<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'house_number',
        'street',
        'barangay',
        'city',
        'province',
        'phone',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
    public function appointments()
    {
    return $this->hasMany(Appointment::class);
    }
}
