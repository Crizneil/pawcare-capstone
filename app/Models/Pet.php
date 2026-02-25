<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',      // The link to the owner
        'pet_id',
        'name',
        'species',
        'gender',
        'birthday',
        'breed',
        'owner',
        'vaccine_type',
        'last_date',
        'next_date',
        'image_url',
        'status',
    ];

    public function vaccinations()
    {
    return $this->hasMany(Vaccination::class);
    }

    public function latestVaccination()
    {
    return $this->hasOne(Vaccination::class)->latestOfMany('date_administered');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => $this->owner ?? 'Guest',
        ]);
    }
    public function getCalculatedStatusAttribute()
    {
        $latest = $this->latestVaccination;

        if (!$latest) return 'unvaccinated';

        // If the record has a 'next_due_date', calculate based on today's date
        if ($latest->next_due_date) {
            $dueDate = Carbon::parse($latest->next_due_date);
            $now = Carbon::now();

            if ($now->gt($dueDate)) return 'overdue';
            if ($now->diffInDays($dueDate) <= 14) return 'due_soon';
        }

        return $latest->status ?? 'fully_vaccinated';
    }
    public function getVaxStatusAttribute()
    {
        $latestVax = $this->latestVaccination;
        $today = Carbon::today();

        // Default values (No Records / Unvaccinated)
        $data = [
            'class' => 'bg-secondary-subtle text-secondary border-secondary',
            'label' => 'No Records',
            'icon' => '',
            'latest_vax' => $latestVax
        ];

        if ($latestVax && $latestVax->next_due_date) {
            $dueDate = Carbon::parse($latestVax->next_due_date);
            $daysUntilDue = $today->diffInDays($dueDate, false);

            if ($daysUntilDue < 0) {
                // Overdue Style (Red)
                $data['class'] = 'bg-danger-subtle text-danger border-danger';
                $data['label'] = 'Overdue';
            } elseif ($daysUntilDue <= 14) {
                // Due Soon Style (Yellow/Orange)
                $data['class'] = 'bg-warning-subtle text-warning border-warning';
                $data['label'] = 'Due Soon';
            } else {
                // Up to Date Style (Green)
                $data['class'] = 'bg-success-subtle text-success border-success';
                $data['label'] = 'Up to Date';
            }
        }

        return (object) $data;
    }
}
