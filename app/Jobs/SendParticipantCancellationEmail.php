<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Participant;
use App\Mail\ParticipantRegistrationCancelled;
use Illuminate\Support\Facades\Mail;

class SendParticipantCancellationEmail implements ShouldQueue
{
    use Queueable;

    /**
     * O número de vezes que o job pode ser tentado.
     */
    public $tries = 3;

    /**
     * O número de segundos para aguardar antes de tentar novamente o job.
     */
    public $backoff = 60;

    public $participantData;

    /**
     * Create a new job instance.
     */
    public function __construct(Participant $participant)
    {
        $this->participantData = [
            'id' => $participant->id,
            'name' => $participant->name,
            'email' => $participant->email,
            'phone' => $participant->phone,
            'document' => $participant->document,
            'event_id' => $participant->event_id,
            'event' => $participant->event,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->participantData['email'])->send(new ParticipantRegistrationCancelled($this->participantData));
    }
}
