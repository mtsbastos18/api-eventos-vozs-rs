<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Jobs\SendEventRegistrationEmail;
use App\Jobs\SendParticipantVerificationEmail;
use App\Jobs\SendParticipantCancellationEmail;

class ParticipantController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'document' => 'required|string|max:20',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        if ($event->participants()->count() >= $event->capacity) {
            return response()->json(['message' => 'O evento já atingiu a capacidade máxima.'], 422);
        }

        $exists = Participant::where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Participante já inscrito neste evento.'], 422);
        }

        // Gerar um código de verificação numérico de 6 dígitos
        $validated['verification_code'] = sprintf('%06d', mt_rand(100000, 999999));
        $validated['is_verified'] = false;

        $participant = Participant::create($validated);

        // Dispatch the job para enviar o código de verificação
        SendParticipantVerificationEmail::dispatch($participant);

        return response()->json([
            'message' => 'Inscrição pré-realizada. Verifique seu e-mail para receber o código de confirmação.',
            'participant' => $participant
        ], 201);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id',
            'code' => 'required|string',
        ]);

        $participant = Participant::where('event_id', $request->event_id)
            ->where('email', $request->email)
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'Participante não encontrado neste evento.'], 404);
        }

        if ($participant->is_verified) {
            return response()->json(['message' => 'O e-mail deste participante já foi verificado.'], 400);
        }

        if ($participant->verification_code !== $request->code) {
            return response()->json(['message' => 'Código de verificação inválido.'], 400);
        }

        $participant->update([
            'is_verified' => true,
            'verification_code' => null,
        ]);

        // Dispara o job de confirmação final do evento
        SendEventRegistrationEmail::dispatch($participant);

        return response()->json(['message' => 'E-mail verificado com sucesso. Inscrição final confirmada e enviada para o e-mail!']);
    }

    public function destroy($eventId, $participantId)
    {
        $event = Event::findOrFail($eventId);
        $participant = Participant::where('id', $participantId)
            ->where('event_id', $event->id)
            ->firstOrFail();
        // Dispara o job de cancelamento de inscrição
        SendParticipantCancellationEmail::dispatch($participant);


        $participant->delete();

        return response()->json(['message' => 'Participante removido com sucesso do evento.']);
    }
}
