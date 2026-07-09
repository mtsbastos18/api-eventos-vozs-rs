<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPostDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'video_path',
        'description',
        'images',
        'flickr_images',
        'youtube_video_url',
    ];

    protected $casts = [
        'images' => 'array',
        'flickr_images' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
