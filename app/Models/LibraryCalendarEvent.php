<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryCalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'type',
        'starts_at',
        'ends_at',
        'is_all_day',
        'location',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_all_day' => 'boolean',
        ];
    }
}
