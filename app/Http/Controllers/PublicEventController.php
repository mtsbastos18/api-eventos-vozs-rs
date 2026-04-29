<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class PublicEventController extends Controller
{
    /**
     * List available events (e.g., future events).
     */
    public function index()
    {
        // Retorna apenas eventos com data maior ou igual a hoje, ordenados pela data mais próxima
        $events = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($events);
    }

    /**
     * Show details of a specific event.
     */
    public function show(Event $event)
    {
        // Opcional: carregar contagem de participantes se quiser mostrar vagas restantes
        $event->loadCount('participants');
        $event->available_spots = $event->capacity - $event->participants_count;

        return response()->json($event);
    }
}
