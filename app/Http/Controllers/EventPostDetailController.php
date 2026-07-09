<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPostDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class EventPostDetailController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        if (now()->lt($event->date)) {
            return response()->json(['error' => 'Só é possível adicionar informações após a data do evento.'], 403);
        }

        $data = $request->validate([
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image',
            'flickrUrl' => 'nullable|string',
            'youtube_video_url' => 'nullable|url',
        ]);

        // 1. Buscar registro existente
        $postDetail = EventPostDetail::where('event_id', $eventId)->first();

        // 2. Processar novas imagens
        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImagePaths[] = $image->storeAs('events', $image->getClientOriginalName(), env('FILESYSTEM_DISK'));
            }
        }

        // 3. Concatenar com as imagens existentes (se houver)
        $existingImages = ($postDetail && is_array($postDetail->images)) ? $postDetail->images : [];
        Log::info('Existing images for event ' . $eventId . ': ' . json_encode($existingImages));
        $finalImages = array_merge($existingImages, $newImagePaths);

        // 4. Lógica para o vídeo (manter o antigo se não enviar um novo)
        $videoPath = $postDetail->video_path ?? null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->storeAs('events', $request->file('video')->getClientOriginalName(), env('FILESYSTEM_DISK'));
        }

        // 5. Salvar ou Atualizar
        $postDetail = EventPostDetail::updateOrCreate(
            ['event_id' => $event->id],
            [
                'video_path' => $videoPath,
                'description' => $data['description'] ?? ($postDetail->description ?? null),
                'images' => $finalImages,
                'youtube_video_url' => $data['youtube_video_url'] ?? ($postDetail->youtube_video_url ?? null),
            ]
        );

        if (!empty($request->flickrUrl)) {
            $this->callFlickrApi($request->flickrUrl, $event->id);
        }

        return response()->json($postDetail, 201);
    }

    private function callFlickrApi($flickrUrl, $eventId)
    {
        // Se flickrUrl estiver presente, faz o curl para a API
        $payload = [
            'url' => $flickrUrl,
            'eventId' => (int) $eventId,
        ];
        $apiUrl = env('FLICKR_API_URL', 'https://crawler-flickr-488916645845.us-central1.run.app/api/scrape'); // Ajuste a URL conforme necessário
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            Log::error('Erro ao enviar flickrUrl para API: ' . curl_error($ch));
        } else {
            Log::info('Resposta da API Flickr: ' . $response . ' (HTTP ' . $httpCode . ')');
        }
        curl_close($ch);
    }

    public function saveFlickrImages(Request $request, $eventId)
    {
        Log::info('Received Flickr images for event ' . $eventId . ': ' . json_encode($request->all()));
        $event = Event::findOrFail($eventId);
        if (now()->lt($event->date)) {
            return response()->json(['error' => 'Só é possível adicionar informações após a data do evento.'], 403);
        }

        $data = $request->validate([
            'flickr_images' => 'required|array',
        ]);

        $postDetail = EventPostDetail::where('event_id', $eventId)->first();
        if (!$postDetail) {
            Log::info('No post detail found for event ' . $eventId);
            return response()->json(['message' => 'Nenhuma informação pós-evento cadastrada.'], 404);
        }

        // Concatenar com as imagens do Flickr existentes (se houver)
        $existingFlickrImages = is_array($postDetail->flickr_images) ? $postDetail->flickr_images : [];
        $finalFlickrImages = array_merge($existingFlickrImages, $data['flickr_images']);

        $postDetail->update([
            'flickr_images' => $finalFlickrImages,
        ]);

        return response()->json($postDetail);
    }

    public function show($eventId)
    {
        $event = Event::where('id', $eventId)
            ->orWhere('slug', $eventId)
            ->firstOrFail();

        $postDetail = $event->postDetail;
        if (!$postDetail) {
            return response()->json(['message' => 'Nenhuma informação pós-evento cadastrada.'], 404);
        }
        return response()->json($postDetail);
    }

    public function update(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $postDetail = $event->postDetail;
        if (!$postDetail) {
            return response()->json(['message' => 'Nenhuma informação pós-evento cadastrada.'], 404);
        }

        $data = $request->validate([
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image',
            'youtube_video_url' => 'nullable|url',
        ]);

        $imagePaths = $postDetail->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->storeAs('events', $image->getClientOriginalName(), env('FILESYSTEM_DISK'));
            }
        }

        $videoPath = $postDetail->video_path;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->storeAs('events', $request->file('video')->getClientOriginalName(), env('FILESYSTEM_DISK'));
        }

        $postDetail->update([
            'video_path' => $videoPath,
            'description' => $data['description'] ?? $postDetail->description,
            'images' => $imagePaths,
            'youtube_video_url' => $data['youtube_video_url'] ?? $postDetail->youtube_video_url,
        ]);

        return response()->json($postDetail);
    }
}
