<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VaccinationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $pet;
    public $status;

    public function __construct($pet, $status)
    {
        $this->pet = $pet;
        $this->status = $status; // 'due_soon' or 'overdue'
    }

    public function build()
    {
        $subject = $this->status === 'overdue'
            ? "Your Pet's Vaccination is Overdue"
            : "Your Pet's Vaccination is Due Soon";

        return $this->subject($subject)
                    ->view('emails.vaccination-reminder');
    }
}
