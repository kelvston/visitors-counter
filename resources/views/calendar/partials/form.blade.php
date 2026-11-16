<form action="{{ $action }}" method="POST" class="card card-body calendar-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <input type="hidden" name="month" value="{{ $month }}">

    <div class="mb-3">
        <label for="calendar-title" class="form-label">Title</label>
        <input id="calendar-title" name="title" class="form-control" maxlength="255"
               value="{{ old('title', $event->title) }}" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="calendar-type" class="form-label">Type</label>
            <select id="calendar-type" name="type" class="form-select" required>
                <option value="event" @selected(old('type', $event->type ?: 'event') === 'event')>Library event</option>
                <option value="holiday" @selected(old('type', $event->type) === 'holiday')>Holiday</option>
                <option value="schedule" @selected(old('type', $event->type) === 'schedule')>Other schedule</option>
            </select>
            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="calendar-location" class="form-label">Location (optional)</label>
            <input id="calendar-location" name="location" class="form-control" maxlength="255"
                   value="{{ old('location', $event->location) }}">
            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="calendar-start" class="form-label">Starts</label>
            <input id="calendar-start" name="starts_at" type="datetime-local" class="form-control"
                   min="{{ $minimumStart }}"
                   value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}" required>
            @error('starts_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="calendar-end" class="form-label">Ends</label>
            <input id="calendar-end" name="ends_at" type="datetime-local" class="form-control"
                   value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}" required>
            @error('ends_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="form-check mb-3">
        <input id="calendar-all-day" name="is_all_day" type="checkbox" class="form-check-input" value="1"
               @checked(old('is_all_day', $event->is_all_day))>
        <label for="calendar-all-day" class="form-check-label">All-day item</label>
    </div>

    <div class="mb-3">
        <label for="calendar-description" class="form-label">Description (optional)</label>
        <textarea id="calendar-description" name="description" rows="4" maxlength="10000"
                  class="form-control">{{ old('description', $event->description) }}</textarea>
        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save calendar item</button>
        <a href="{{ route('calendar.index', ['month' => $month]) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
