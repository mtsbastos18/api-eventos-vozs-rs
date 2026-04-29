<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'location',
        'description',
        'capacity',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
