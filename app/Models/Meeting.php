<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'section',
        'title',
        'meeting_at',
        'venue',
        'attendees',
        'attendance',
        'minutes',
        'minutes_attachment',
        'audio_attachment',
    ];

    protected function casts(): array
    {
        return [
            'meeting_at' => 'datetime',
            'attendance' => 'array',
        ];
    }

    public function getAttendanceSummaryAttribute(): ?string
    {
        if (! $this->attendance) {
            return $this->attendees;
        }

        return collect($this->attendance)
            ->map(fn (array $attendee) => $attendee['name'].' — '.match ($attendee['status']) {
                'present' => 'Present',
                'absent' => 'Absent',
                'absent_with_permission' => 'Absent with permission',
                default => 'Unknown',
            })
            ->implode(', ');
    }
}
