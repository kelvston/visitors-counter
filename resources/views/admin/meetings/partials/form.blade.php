<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="compact-entry-form w-100 p-3 shadow rounded bg-light">
    @csrf
    <input type="hidden" name="attendance_submitted" value="1">
    @if ($method !== 'POST')
        @method($method)
    @endif
    @php
        $selectedAttendance = collect(old('attendance', $meeting?->attendance ?? []))
            ->keyBy(fn ($attendee) => (string) ($attendee['user_id'] ?? ''));
    @endphp

    <div class="mb-3">
        <label for="title" class="form-label">Meeting title or topic</label>
        <input id="title" name="title" type="text" class="form-control"
               value="{{ old('title', $meeting?->title) }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="row g-3">
        <div class="col-md-6 mb-3">
            <label for="meeting_at" class="form-label">Date and time</label>
            <input id="meeting_at" name="meeting_at" type="datetime-local" class="form-control"
                   value="{{ old('meeting_at', $meeting?->meeting_at?->format('Y-m-d\TH:i')) }}" required>
            @error('meeting_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="venue" class="form-label">Venue</label>
            <input id="venue" name="venue" type="text" class="form-control"
                   value="{{ old('venue', $meeting?->venue) }}" required>
            @error('venue') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="attendance-search" class="form-label">Attendance</label>
        <input id="attendance-search" type="search" class="form-control mb-2"
               placeholder="Search staff by name or email" data-attendance-search>
        @if ($meeting?->attendees && ! $meeting?->attendance)
            <div class="alert alert-info py-2 mb-2">
                Previously recorded attendees: {{ $meeting->attendees }}
                <input type="hidden" name="attendees" value="{{ $meeting->attendees }}">
            </div>
        @endif
        <div class="attendee-options" data-attendee-options>
            @forelse ($staff as $index => $person)
                @php
                    $attendee = $selectedAttendance->get((string) $person->id);
                    $searchText = mb_strtolower($person->name.' '.$person->email);
                @endphp
                <div class="attendee-option" data-attendee-option data-search-text="{{ $searchText }}">
                    <label class="attendee-person">
                        <input class="form-check-input mt-0" type="checkbox"
                               name="attendance[{{ $index }}][user_id]" value="{{ $person->id }}"
                               data-attendee-toggle @checked($attendee)>
                        <span>
                            <strong>{{ $person->name }}</strong>
                            <small>{{ $person->email }}</small>
                        </span>
                    </label>
                    <select class="form-select form-select-sm" name="attendance[{{ $index }}][status]"
                            aria-label="Attendance status for {{ $person->name }}" data-attendee-status
                            @disabled(! $attendee)>
                        <option value="present" @selected(($attendee['status'] ?? 'present') === 'present')>Present</option>
                        <option value="absent" @selected(($attendee['status'] ?? '') === 'absent')>Absent</option>
                        <option value="absent_with_permission"
                                @selected(($attendee['status'] ?? '') === 'absent_with_permission')>
                            Absent with permission
                        </option>
                    </select>
                </div>
            @empty
                <p class="text-muted mb-0">
                    No {{ $routePrefix === 'academics.meetings' ? 'academic' : 'administrative' }} staff are assigned to this category yet.
                    An administrator can assign staff in Settings &gt; Manage Users.
                </p>
            @endforelse
            <p class="text-muted mb-0" data-attendee-empty hidden>No staff match your search.</p>
        </div>
        @error('attendance') <small class="text-danger">{{ $message }}</small> @enderror
        @error('attendance.*.user_id') <small class="text-danger">{{ $message }}</small> @enderror
        @error('attendance.*.status') <small class="text-danger">{{ $message }}</small> @enderror
        @error('attendees') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label for="minutes" class="form-label">Minutes</label>
        <textarea id="minutes" name="minutes" class="form-control" rows="5" required>{{ old('minutes', $meeting?->minutes) }}</textarea>
        @error('minutes') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label for="minutes_attachment" class="form-label">Minutes PDF (optional, max 10 MB)</label>
        <input id="minutes_attachment" name="minutes_attachment" type="file" class="form-control" accept="application/pdf">
        @if ($meeting?->minutes_attachment)
            <small class="text-muted">A PDF is already attached. Upload a new one to replace it.</small>
        @endif
        @error('minutes_attachment') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3" data-audio-recorder>
        <label for="audio_attachment" class="form-label">Meeting audio (MP3, WAV, OGG, WebM, or M4A; max 50 MB)</label>
        <input id="audio_attachment" name="audio_attachment" type="file" class="form-control"
               accept=".mp3,.wav,.ogg,.webm,.m4a,audio/mpeg,audio/wav,audio/ogg,audio/webm,audio/mp4">
        <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-outline-primary btn-sm" data-record-start>Start recording</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-record-stop disabled>Stop recording</button>
        </div>
        <p class="small text-muted mt-2 mb-1" data-record-status aria-live="polite">
            No audio selected. Start and stop a recording, or choose an audio file before saving.
        </p>
        @if ($meeting?->audio_attachment)
            <p class="small text-muted mb-1">An audio recording is attached. Upload or record a new one to replace it.</p>
        @endif
        <audio class="w-100 mt-2" controls data-audio-preview
               @if (! $meeting?->audio_attachment) hidden @endif
               @if ($meeting?->audio_attachment) src="{{ route($routePrefix.'.audio', $meeting) }}" @endif>
            Your browser does not support audio playback.
        </audio>
        @error('audio_attachment') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save meeting</button>
        <a href="{{ route($routePrefix.'.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

@push('scripts')
    <script>
        document.querySelectorAll('[data-audio-recorder]').forEach(function (container) {
            var form = container.closest('form');
            var input = container.querySelector('[name="audio_attachment"]');
            var preview = container.querySelector('[data-audio-preview]');
            var startButton = container.querySelector('[data-record-start]');
            var stopButton = container.querySelector('[data-record-stop]');
            var status = container.querySelector('[data-record-status]');
            var recorder;
            var chunks = [];
            var stream;
            var recordingFinalizing = false;

            function showSelectedAudio(file) {
                if (!file) return;

                preview.src = URL.createObjectURL(file);
                preview.hidden = false;
                preview.load();
            }

            input.addEventListener('change', function () {
                var file = input.files[0];
                if (!file) return;

                showSelectedAudio(file);
                status.textContent = 'Selected: ' + file.name + ' (' + Math.ceil(file.size / 1024) + ' KB). Save the meeting to attach it.';
            });

            form.addEventListener('submit', function (event) {
                if ((recorder && recorder.state === 'recording') || recordingFinalizing) {
                    event.preventDefault();
                    status.textContent = 'Stop the recording and wait for it to finish before saving.';
                }
            });

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !window.MediaRecorder) {
                status.textContent = 'Audio recording is not supported in this browser. You can still attach an audio file.';
                startButton.disabled = true;
                return;
            }

            startButton.addEventListener('click', async function () {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    var mimeType = ['audio/webm', 'audio/ogg', 'audio/mp4']
                        .find(function (type) { return MediaRecorder.isTypeSupported(type); });
                    recorder = mimeType ? new MediaRecorder(stream, { mimeType: mimeType }) : new MediaRecorder(stream);
                    chunks = [];

                    recorder.addEventListener('dataavailable', function (event) {
                        if (event.data.size) chunks.push(event.data);
                    });
                    recorder.addEventListener('stop', function () {
                        recordingFinalizing = false;
                        if (!chunks.length) {
                            status.textContent = 'No audio was captured. Try recording again or choose an audio file.';
                            stream.getTracks().forEach(function (track) { track.stop(); });
                            return;
                        }

                        var type = recorder.mimeType || 'audio/webm';
                        var extension = type.includes('ogg') ? 'ogg' : (type.includes('mp4') ? 'm4a' : 'webm');
                        var recording = new File(chunks, 'meeting-recording.' + extension, { type: type });
                        var transfer = new DataTransfer();
                        transfer.items.add(recording);
                        input.files = transfer.files;
                        showSelectedAudio(recording);
                        status.textContent = 'Recording ready: ' + recording.name + ' (' + Math.ceil(recording.size / 1024) + ' KB). Save the meeting to attach it.';
                        stream.getTracks().forEach(function (track) { track.stop(); });
                    });

                    recorder.start();
                    startButton.disabled = true;
                    stopButton.disabled = false;
                    status.textContent = 'Recording…';
                } catch (error) {
                    status.textContent = 'Could not start recording. Check microphone permission or attach an audio file instead.';
                }
            });

            stopButton.addEventListener('click', function () {
                if (recorder && recorder.state === 'recording') {
                    recordingFinalizing = true;
                    recorder.stop();
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    status.textContent = 'Finishing recording…';
                }
            });
        });

        document.querySelectorAll('[data-attendance-search]').forEach(function (search) {
            var form = search.closest('form');
            var options = form.querySelector('[data-attendee-options]');
            var rows = options.querySelectorAll('[data-attendee-option]');
            var emptyMessage = options.querySelector('[data-attendee-empty]');

            function filterStaff() {
                var query = search.value.trim().toLowerCase();
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var matches = row.dataset.searchText.includes(query);
                    row.hidden = !matches;
                    visibleCount += matches ? 1 : 0;
                });

                emptyMessage.hidden = visibleCount > 0;
            }

            search.addEventListener('input', filterStaff);
            options.querySelectorAll('[data-attendee-toggle]').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    checkbox.closest('.attendee-option')
                        .querySelector('[data-attendee-status]').disabled = !checkbox.checked;
                });
            });
        });
    </script>
@endpush
