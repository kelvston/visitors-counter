@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Academic Meeting Minutes</h1>
            @if ($canManage)
                <a href="{{ route('academics.meetings.create') }}" class="btn btn-primary">Record academic meeting</a>
            @endif
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('academics.meetings.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-sm-4 col-md-3">
                <label for="meeting-from" class="form-label">From date</label>
                <input id="meeting-from" type="date" name="from" class="form-control"
                       value="{{ request('from') }}" max="{{ request('to') }}">
            </div>
            <div class="col-sm-4 col-md-3">
                <label for="meeting-to" class="form-label">To date</label>
                <input id="meeting-to" type="date" name="to" class="form-control"
                       value="{{ request('to') }}" min="{{ request('from') }}">
            </div>
            <div class="col-sm-4 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('academics.meetings.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                <tr>
                    <th>Meeting</th>
                    <th>Date and time</th>
                    <th>Venue</th>
                    <th>Attendees</th>
                    <th>Minutes</th>
                    <th>Audio</th>
                    <th>PDF</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($meetings as $meeting)
                    <tr>
                        <td>{{ $meeting->title }}</td>
                        <td>{{ $meeting->meeting_at->format('d M Y, H:i') }}</td>
                        <td>{{ $meeting->venue }}</td>
                        <td>{{ $meeting->attendance_summary ?: '—' }}</td>
                        <td>{{ $meeting->minutes }}</td>
                        <td>
                            @if ($meeting->audio_attachment)
                                <audio controls preload="none" style="max-width: 220px"
                                       src="{{ route('academics.meetings.audio', $meeting) }}"></audio>
                                <a class="d-block small" href="{{ route('academics.meetings.audio', $meeting) }}"
                                   target="_blank" rel="noopener">Open recording</a>
                            @else
                                <span class="text-muted">No audio attached</span>
                            @endif
                        </td>
                        <td>
                            @if ($meeting->minutes_attachment)
                                <a href="{{ route('academics.meetings.minutes.download', $meeting) }}"
                                   class="btn btn-sm btn-outline-primary">Download PDF</a>
                            @else
                                <span class="text-muted">No PDF</span>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('academics.meetings.edit', $meeting) }}"
                                   class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('academics.meetings.destroy', $meeting) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Remove this academic meeting record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No academic meeting minutes available.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $meetings->links() }}
    </div>
@endsection
