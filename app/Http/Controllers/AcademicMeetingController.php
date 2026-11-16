<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use App\Rules\MeetingAudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicMeetingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = Meeting::query()->where('section', 'academic');

        if (! empty($filters['from'])) {
            $query->whereDate('meeting_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('meeting_at', '<=', $filters['to']);
        }

        $meetings = $query->latest('meeting_at')->paginate(15)->withQueryString();

        $canManage = $request->user()->canManageMinutesFor('academic');

        return view('academics.meetings.index', compact('meetings', 'canManage'));
    }

    public function create(): View
    {
        $staff = User::query()
            ->where('staff_category', 'academic')
            ->where('is_admin', false)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('academics.meetings.create', compact('staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMeeting($request);

        if ($request->hasFile('minutes_attachment')) {
            $validated['minutes_attachment'] = $request->file('minutes_attachment')
                ->store('meetings/academic-minutes', 'local');
        }

        if ($request->hasFile('audio_attachment')) {
            $validated['audio_attachment'] = $request->file('audio_attachment')
                ->store('meetings/academic-audio', 'local');
        }

        Meeting::create($validated + ['section' => 'academic']);

        return redirect()->route('academics.meetings.index')->with('status', 'Academic meeting minutes recorded.');
    }

    public function edit(Meeting $meeting): View
    {
        $meeting = $this->academicMeeting($meeting);
        $staff = User::query()
            ->where('staff_category', 'academic')
            ->where('is_admin', false)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('academics.meetings.edit', compact('meeting', 'staff'));
    }

    public function update(Request $request, Meeting $meeting): RedirectResponse
    {
        $meeting = $this->academicMeeting($meeting);
        $validated = $this->validateMeeting($request);
        $oldAttachment = $meeting->minutes_attachment;
        $oldAudioAttachment = $meeting->audio_attachment;

        if ($request->hasFile('minutes_attachment')) {
            $validated['minutes_attachment'] = $request->file('minutes_attachment')
                ->store('meetings/academic-minutes', 'local');
        }

        if ($request->hasFile('audio_attachment')) {
            $validated['audio_attachment'] = $request->file('audio_attachment')
                ->store('meetings/academic-audio', 'local');
        }

        $meeting->update($validated);

        if ($request->hasFile('minutes_attachment') && $oldAttachment) {
            Storage::disk('local')->delete($oldAttachment);
        }
        if ($request->hasFile('audio_attachment') && $oldAudioAttachment) {
            Storage::disk('local')->delete($oldAudioAttachment);
        }

        return redirect()->route('academics.meetings.index')->with('status', 'Academic meeting minutes updated.');
    }

    public function downloadMinutes(Meeting $meeting): StreamedResponse
    {
        $meeting = $this->academicMeeting($meeting);
        abort_unless(
            $meeting->minutes_attachment && Storage::disk('local')->exists($meeting->minutes_attachment),
            404
        );

        $filename = (Str::slug($meeting->title) ?: 'academic-meeting-minutes').'.pdf';

        return Storage::disk('local')->download($meeting->minutes_attachment, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function playAudio(Meeting $meeting): BinaryFileResponse
    {
        $meeting = $this->academicMeeting($meeting);
        abort_unless(
            $meeting->audio_attachment && Storage::disk('local')->exists($meeting->audio_attachment),
            404
        );

        $mimeType = match (strtolower(pathinfo($meeting->audio_attachment, PATHINFO_EXTENSION))) {
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'webm' => 'audio/webm',
            'm4a' => 'audio/mp4',
            default => 'application/octet-stream',
        };

        return response()->file(Storage::disk('local')->path($meeting->audio_attachment), [
            'Content-Type' => $mimeType,
        ]);
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $meeting = $this->academicMeeting($meeting);

        if ($meeting->minutes_attachment) {
            Storage::disk('local')->delete($meeting->minutes_attachment);
        }
        if ($meeting->audio_attachment) {
            Storage::disk('local')->delete($meeting->audio_attachment);
        }

        $meeting->delete();

        return redirect()->route('academics.meetings.index')->with('status', 'Academic meeting record removed.');
    }

    private function academicMeeting(Meeting $meeting): Meeting
    {
        abort_unless($meeting->section === 'academic', 404);

        return $meeting;
    }

    private function validateMeeting(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meeting_at' => ['required', 'date'],
            'venue' => ['required', 'string', 'max:255'],
            'attendees' => ['nullable', 'string', 'max:10000'],
            'attendance_submitted' => ['sometimes', 'accepted'],
            'attendance' => ['nullable', 'array', 'max:100'],
            'attendance.*' => ['required', 'array'],
            'attendance.*.user_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('staff_category', 'academic')
                    ->where('is_admin', false)
                    ->whereNull('deleted_at')),
            ],
            'attendance.*.status' => ['required', 'in:present,absent,absent_with_permission'],
            'minutes' => ['required', 'string'],
            'minutes_attachment' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
            'audio_attachment' => [
                'nullable',
                'file',
                'extensions:mp3,wav,ogg,webm,m4a',
                new MeetingAudio,
                'max:51200',
            ],
        ]);

        if ($request->boolean('attendance_submitted')) {
            $validated['attendance'] = $this->resolveAttendance($validated['attendance'] ?? []);

            if ($validated['attendance'] !== []) {
                $validated['attendees'] = null;
            }
        }

        return $validated;
    }

    private function resolveAttendance(array $attendance): array
    {
        $users = User::query()
            ->whereIn('id', collect($attendance)->pluck('user_id'))
            ->get(['id', 'name'])
            ->keyBy('id');

        return collect($attendance)
            ->map(fn (array $attendee) => [
                'user_id' => (int) $attendee['user_id'],
                'name' => $users->get($attendee['user_id'])->name,
                'status' => $attendee['status'],
            ])
            ->values()
            ->all();
    }
}
