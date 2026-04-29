<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Participant;
use App\Mail\ParticipantEmailVerification;
use Illuminate\Support\Facades\Mail;

class SendParticipantVerificationEmail implements ShouldQueue
{
    use Queueable;

    public $participant;

    /**
     * Create a new job instance.
     */
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->participant->email)->send(new ParticipantEmailVerification($this->participant));
    }
}
