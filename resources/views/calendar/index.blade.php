@extends('layouts.header')

@section('content')
    <div class="container mt-4 calendar-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1">Library Calendar</h1>
                <p class="text-muted mb-0">Library events, holidays, and schedules</p>
            </div>
            @if ($canManage)
                <a href="{{ route('calendar.create', ['month' => $month->format('Y-m')]) }}"
                   class="btn btn-primary">Add calendar item</a>
            @endif
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <section class="card calendar-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('calendar.index', ['month' => $month->subMonth()->format('Y-m')]) }}"
                       aria-label="Previous month">&lsaquo;</a>
                    <h2 class="h5 mb-0">{{ $month->format('F Y') }}</h2>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('calendar.index', ['month' => $month->addMonth()->format('Y-m')]) }}"
                       aria-label="Next month">&rsaquo;</a>
                </div>
                <a class="btn btn-sm btn-outline-primary"
                   href="{{ route('calendar.index', ['month' => now()->format('Y-m')]) }}">Today</a>
            </div>

            <div class="calendar-grid" role="grid" aria-label="Library events for {{ $month->format('F Y') }}">
                @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekdayIndex => $weekday)
                    <div class="calendar-weekday {{ $weekdayIndex > 0 && $weekdayIndex < 6 ? 'calendar-weekday-working' : '' }}"
                         role="columnheader">{{ $weekday }}</div>
                @endforeach

                @foreach ($weeks as $week)
                    @foreach ($week as $day)
                        <button type="button"
                                class="calendar-day {{ $day['date']->month !== $month->month ? 'calendar-day-outside' : '' }} {{ $day['date']->isWeekday() ? 'calendar-day-working' : '' }} {{ $day['date']->lt($today) ? 'calendar-day-past' : '' }}"
                                role="gridcell" aria-label="{{ $day['date']->format('l, F j, Y') }}"
                                data-calendar-date="{{ $day['date']->format('Y-m-d') }}"
                                data-calendar-label="{{ $day['date']->format('l, F j, Y') }}"
                                data-calendar-can-file="{{ $canManage && $day['date']->greaterThanOrEqualTo($today) ? 'true' : 'false' }}"
                                data-calendar-events="{{ $day['events']->map(fn ($event) => [
                                    'title' => $event->title,
                                    'type' => $event->type,
                                    'starts_at' => $event->starts_at->format('Y-m-d\TH:i'),
                                    'ends_at' => $event->ends_at->format('Y-m-d\TH:i'),
                                    'start_label' => $event->is_all_day ? 'All day' : $event->starts_at->format('H:i'),
                                    'time_label' => $event->is_all_day
                                        ? 'All day'
                                        : $event->starts_at->format('D, d M Y H:i').' – '.$event->ends_at->format('D, d M Y H:i'),
                                    'location' => $event->location,
                                    'description' => $event->description,
                                    'is_all_day' => $event->is_all_day,
                                ])->toJson() }}">
                            <span class="calendar-day-number {{ $day['date']->isToday() ? 'calendar-today' : '' }}">
                                {{ $day['date']->day }}
                            </span>
                            <span class="calendar-day-events">
                                @foreach ($day['events']->take(2) as $event)
                                    <span class="calendar-event calendar-event-{{ $event->type }}">
                                        <span class="calendar-event-time">{{ $event->is_all_day ? 'All day' : $event->starts_at->format('H:i') }}</span>
                                        <strong>{{ $event->title }}</strong>
                                    </span>
                                @endforeach
                                @if ($day['events']->count() > 2)
                                    <span class="calendar-more-events">+{{ $day['events']->count() - 2 }} more</span>
                                @endif
                            </span>
                        </button>
                    @endforeach
                @endforeach
            </div>
            <div class="calendar-legend">
                <span><i class="calendar-legend-dot calendar-event-event"></i>Event</span>
                <span><i class="calendar-legend-dot calendar-event-holiday"></i>Holiday</span>
                <span><i class="calendar-legend-dot calendar-event-schedule"></i>Schedule</span>
                <span><i class="calendar-working-day-key"></i>Working day (Monday–Friday)</span>
            </div>
        </section>

        <section class="mt-4">
            <h2 class="h5">Items in {{ $month->format('F Y') }}</h2>
            @php($monthItems = collect($weeks)->flatten(1)->flatMap(fn ($day) => $day['events'])->unique('id')->sortBy('starts_at'))
            @forelse ($monthItems as $event)
                <article class="card calendar-item-card mb-2">
                    <div class="card-body d-flex flex-wrap justify-content-between gap-3">
                        <div>
                            <span class="badge calendar-badge-{{ $event->type }}">{{ ucfirst($event->type) }}</span>
                            <strong class="ms-1">{{ $event->title }}</strong>
                            <div class="small text-muted mt-1">
                                {{ $event->starts_at->format('D, d M Y H:i') }}
                                @if ($event->ends_at->toDateString() !== $event->starts_at->toDateString())
                                    – {{ $event->ends_at->format('D, d M Y H:i') }}
                                @endif
                                @if ($event->location) · {{ $event->location }} @endif
                            </div>
                            @if ($event->description)
                                <p class="mb-0 mt-2">{{ $event->description }}</p>
                            @endif
                        </div>
                        @if ($canManage)
                            <div class="d-flex gap-2">
                                <a href="{{ route('calendar.edit', ['event' => $event, 'month' => $month->format('Y-m')]) }}"
                                   class="btn btn-sm btn-outline-primary align-self-start">Edit</a>
                                <form action="{{ route('calendar.destroy', $event) }}" method="POST"
                                      onsubmit="return confirm('Remove this calendar item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-muted">No items scheduled for this month.</p>
            @endforelse
        </section>

        <div class="modal fade" id="calendar-day-modal" tabindex="-1" aria-labelledby="calendar-day-modal-title"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title h5 mb-0" id="calendar-day-modal-title">Calendar date</h2>
                            <p class="small text-muted mb-0" data-calendar-modal-subtitle></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <section class="mb-3">
                            <h3 class="h6">Activities on this date</h3>
                            <div class="calendar-modal-events" data-calendar-modal-events></div>
                        </section>
                        @if ($canManage)
                            <p class="small text-muted mb-0" data-calendar-past-notice hidden>
                                New items can only be filed for today or a future date.
                            </p>
                            <section data-calendar-file-section hidden>
                                <hr>
                                <h3 class="h6 mb-3">File an event or upcoming activity</h3>
                                <form action="{{ route('calendar.store') }}" method="POST" class="calendar-modal-form">
                                @csrf
                                <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                                <div class="mb-2">
                                    <label for="modal-calendar-title" class="form-label">Title</label>
                                    <input id="modal-calendar-title" name="title" class="form-control" maxlength="255"
                                           value="{{ old('title') }}" required>
                                    @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="modal-calendar-type" class="form-label">Type</label>
                                        <select id="modal-calendar-type" name="type" class="form-select" required>
                                            <option value="event" @selected(old('type') === 'event')>Library event</option>
                                            <option value="holiday" @selected(old('type') === 'holiday')>Holiday</option>
                                            <option value="schedule" @selected(old('type') === 'schedule')>Other schedule</option>
                                        </select>
                                        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-calendar-location" class="form-label">Location (optional)</label>
                                        <input id="modal-calendar-location" name="location" class="form-control"
                                               maxlength="255" value="{{ old('location') }}">
                                        @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-calendar-start" class="form-label">Starts</label>
                                        <input id="modal-calendar-start" name="starts_at" type="datetime-local"
                                               class="form-control" min="{{ $today->format('Y-m-d\T00:00') }}"
                                               value="{{ old('starts_at') }}" required>
                                        @error('starts_at') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-calendar-end" class="form-label">Ends</label>
                                        <input id="modal-calendar-end" name="ends_at" type="datetime-local"
                                               class="form-control" value="{{ old('ends_at') }}" required>
                                        @error('ends_at') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-check my-2">
                                    <input id="modal-calendar-all-day" name="is_all_day" type="checkbox"
                                           class="form-check-input" value="1" @checked(old('is_all_day'))>
                                    <label for="modal-calendar-all-day" class="form-check-label">All-day item</label>
                                </div>
                                <div class="mb-3">
                                    <label for="modal-calendar-description" class="form-label">Description (optional)</label>
                                    <textarea id="modal-calendar-description" name="description" rows="2"
                                              maxlength="10000" class="form-control">{{ old('description') }}</textarea>
                                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Save calendar item</button>
                                </form>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .calendar-card { overflow: hidden; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
        .calendar-weekday { padding: .35rem .25rem; background: #f5f7f0; color: #47551d; font-size: .82rem; font-weight: 600; text-align: center; }
        .calendar-weekday-working { background: #eaf0dc; }
        .calendar-day { display: block; width: 100%; min-width: 0; min-height: 78px; padding: .28rem; border: 0; border-top: 1px solid #e6e9df; border-right: 1px solid #e6e9df; background: #fff; color: inherit; text-align: left; }
        .calendar-day:hover, .calendar-day:focus-visible { background: #f7f9f2; outline: 2px solid #80902d; outline-offset: -2px; }
        .calendar-day:nth-child(7n + 7) { border-right: 0; }
        .calendar-day-outside { background: #fafbf8; color: #9a9e90; }
        .calendar-day-working { background: #fbfcf8; }
        .calendar-day-working:hover, .calendar-day-working:focus-visible { background: #f0f4e7; }
        .calendar-day-past { color: #8d9187; }
        .calendar-day-number { display: inline-grid; width: 1.4rem; height: 1.4rem; place-items: center; border-radius: 50%; font-size: .78rem; }
        .calendar-today { background: #52671a; color: white; }
        .calendar-day-events { display: grid; gap: .15rem; margin-top: .15rem; }
        .calendar-event { display: grid; grid-template-columns: auto minmax(0, 1fr); align-items: baseline; gap: .2rem; padding: .12rem .2rem; border-left: 2px solid #52671a; border-radius: .15rem; background: #f1f5e7; color: #30391b; font-size: .65rem; line-height: 1.15; }
        .calendar-event-holiday { border-left-color: #b2463a; background: #fff0ed; }
        .calendar-event-schedule { border-left-color: #3c72a0; background: #edf5fb; }
        .calendar-event strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .calendar-event-time { white-space: nowrap; color: #666; }
        .calendar-more-events { color: #596a19; font-size: .65rem; }
        .calendar-legend { display: flex; flex-wrap: wrap; gap: .75rem; padding: .5rem .65rem; border-top: 1px solid #e6e9df; font-size: .75rem; }
        .calendar-legend > span { display: inline-flex; align-items: center; gap: .3rem; }
        .calendar-legend-dot { display: inline-block; width: .6rem; height: .6rem; border-radius: 50%; background: #52671a; }
        .calendar-legend-dot.calendar-event-holiday { background: #b2463a; }
        .calendar-legend-dot.calendar-event-schedule { background: #3c72a0; }
        .calendar-working-day-key { display: inline-block; width: .6rem; height: .6rem; border: 1px solid #d5dfbf; background: #eaf0dc; }
        .calendar-badge-event { background: #52671a; }
        .calendar-badge-holiday { background: #b2463a; }
        .calendar-badge-schedule { background: #3c72a0; }
        .calendar-modal-event { padding: .65rem .75rem; border: 1px solid #e6e9df; border-left: 3px solid #52671a; border-radius: .35rem; }
        .calendar-modal-event + .calendar-modal-event { margin-top: .5rem; }
        .calendar-modal-event-holiday { border-left-color: #b2463a; }
        .calendar-modal-event-schedule { border-left-color: #3c72a0; }
        .calendar-modal-form .form-label { margin-bottom: .2rem; font-size: .88rem; }
        .calendar-day-modal-errors:empty { display: none; }
        @media (max-width: 767.98px) {
            .calendar-day { min-height: 58px; padding: .18rem; }
            .calendar-weekday { font-size: .65rem; }
            .calendar-event { grid-template-columns: minmax(0, 1fr); font-size: .6rem; }
            .calendar-event-time { display: none; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('calendar-day-modal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            var modalTitle = document.getElementById('calendar-day-modal-title');
            var eventsContainer = modalElement.querySelector('[data-calendar-modal-events]');
            var startInput = document.getElementById('modal-calendar-start');
            var endInput = document.getElementById('modal-calendar-end');
            var monthInput = modalElement.querySelector('input[name="month"]');
            var fileSection = modalElement.querySelector('[data-calendar-file-section]');
            var pastDateNotice = modalElement.querySelector('[data-calendar-past-notice]');

            function showEvents(events) {
                eventsContainer.replaceChildren();
                if (!events.length) {
                    var empty = document.createElement('p');
                    empty.className = 'text-muted small mb-0';
                    empty.textContent = 'No activities scheduled for this date.';
                    eventsContainer.append(empty);
                    return;
                }

                events.forEach(function (event) {
                    var card = document.createElement('article');
                    var heading = document.createElement('div');
                    var badge = document.createElement('span');
                    var title = document.createElement('strong');
                    var time = document.createElement('p');
                    card.className = 'calendar-modal-event calendar-modal-event-' + event.type;
                    badge.className = 'badge calendar-badge-' + event.type + ' me-2';
                    badge.textContent = event.type.charAt(0).toUpperCase() + event.type.slice(1);
                    title.textContent = event.title;
                    time.className = 'small text-muted mb-1';
                    time.textContent = event.time_label + (event.location ? ' · ' + event.location : '');
                    heading.append(badge, title);
                    card.append(heading, time);
                    if (event.description) {
                        var description = document.createElement('p');
                        description.className = 'small mb-0';
                        description.textContent = event.description;
                        card.append(description);
                    }
                    eventsContainer.append(card);
                });
            }

            document.querySelectorAll('[data-calendar-date]').forEach(function (dayButton) {
                dayButton.addEventListener('click', function () {
                    var date = dayButton.dataset.calendarDate;
                    var events = JSON.parse(dayButton.dataset.calendarEvents);
                    modalTitle.textContent = dayButton.dataset.calendarLabel;
                    showEvents(events);

                    if (fileSection && pastDateNotice) {
                        var canFile = dayButton.dataset.calendarCanFile === 'true';
                        fileSection.hidden = !canFile;
                        pastDateNotice.hidden = canFile;
                    }

                    if (startInput && endInput) {
                        startInput.value = date + 'T09:00';
                        endInput.value = date + 'T10:00';
                        monthInput.value = date.slice(0, 7);
                    }

                    modal.show();
                });
            });

            @if ($canManage && $errors->any())
                modalTitle.textContent = 'File an event or upcoming activity';
                showEvents([]);
                @if (old('starts_at') && substr(old('starts_at'), 0, 10) < $today->format('Y-m-d'))
                    fileSection.hidden = true;
                    pastDateNotice.hidden = false;
                @else
                    fileSection.hidden = false;
                    pastDateNotice.hidden = true;
                @endif
                @if (old('starts_at'))
                    startInput.value = @json(old('starts_at'));
                @endif
                @if (old('ends_at'))
                    endInput.value = @json(old('ends_at'));
                @endif
                modal.show();
            @endif
        });
    </script>
@endpush
