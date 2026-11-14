<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class MeetingAudio implements ValidationRule
{
    private const MIME_TYPES = [
        'mp3' => ['audio/mpeg', 'audio/mp3'],
        'wav' => ['audio/wav', 'audio/x-wav', 'audio/vnd.wave'],
        'ogg' => ['audio/ogg', 'application/ogg', 'video/ogg'],
        'webm' => ['audio/webm', 'video/webm'],
        'm4a' => ['audio/mp4', 'video/mp4', 'application/mp4'],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be a valid audio file.');

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = $value->getMimeType();
        $isWebmContainer = $extension === 'webm'
            && $mimeType === 'application/octet-stream'
            && file_get_contents($value->getRealPath(), false, null, 0, 4) === "\x1A\x45\xDF\xA3";

        if (! in_array($mimeType, self::MIME_TYPES[$extension] ?? [], true) && ! $isWebmContainer) {
            $fail('The :attribute must be an MP3, WAV, OGG, WebM, or M4A audio file.');
        }
    }
}
