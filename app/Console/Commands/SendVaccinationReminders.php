<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pet;
use Illuminate\Support\Facades\Mail;
use App\Mail\VaccinationReminder;
use Carbon\Carbon;

class SendVaccinationReminders extends Command
{
    protected $signature = 'vaccinations:reminder';
    protected $description = 'Send reminders for due soon and overdue pet vaccinations';

    public function handle()
    {
        $today = Carbon::today();

        // --- Due soon: within 14 days
        $dueSoonPets = Pet::notDeceased()->with('user', 'latestVaccination')
            ->whereHas('latestVaccination', function ($q) use ($today) {
                $q->whereBetween('next_due_date', [$today, $today->copy()->addDays(14)]);
            })
            ->get();

        foreach ($dueSoonPets as $pet) {
            if ($pet->user && $pet->latestVaccination) {
                Mail::to($pet->user->email)->send(new VaccinationReminder($pet, 'due_soon'));
            }
        }

        // --- Overdue: next_due_date < today
        $overduePets = Pet::notDeceased()->with('user', 'latestVaccination')
            ->whereHas('latestVaccination', function ($q) use ($today) {
                $q->where('next_due_date', '<', $today);
            })
            ->get();

        foreach ($overduePets as $pet) {
            if ($pet->user && $pet->latestVaccination) {
                Mail::to($pet->user->email)->send(new VaccinationReminder($pet, 'overdue'));
            }
        }

        $this->info('Vaccination reminders sent successfully!');
    }
}
