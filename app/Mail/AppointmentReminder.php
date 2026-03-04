<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $type; // 'approved', 'rejected', 'rescheduled'

    public function __construct($appointment, $type = 'approved')
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    public function build()
    {
        // Set subject dynamically
        $subject = match($this->type) {
            'approved'   => 'PawCare Appointment Approved',
            'rejected'   => 'PawCare Appointment Rejected',
            'rescheduled'=> 'PawCare Appointment Rescheduled',
            default      => 'PawCare Appointment Update',
        };

        return $this->subject($subject)
                ->view('emails.appointment-status')
                ->with([
                    'appointment' => $this->appointment,
                    'type' => $this->type, // ✅ Pass the type to Blade
                ]);
    }
}
