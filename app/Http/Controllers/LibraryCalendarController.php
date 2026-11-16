<?php

namespace App\Http\Controllers;

use App\Models\LibraryCalendarEvent;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LibraryCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);
        $month = CarbonImmutable::createFromFormat('!Y-m', $validated['month'] ?? now()->format('Y-m'));
        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();
        $today = CarbonImmutable::today();

        $monthEvents = LibraryCalendarEvent::query()
            ->where('starts_at', '<=', $monthEnd->endOfDay())
            ->where('ends_at', '>=', $monthStart->startOfDay())
            ->orderBy('starts_at')
            ->get();
        $events = collect();

        foreach ($monthEvents as $event) {
            $firstDay = $event->starts_at->max($monthStart)->startOfDay();
            $lastDay = $event->ends_at->min($monthEnd)->startOfDay();

            for ($day = $firstDay; $day <= $lastDay; $day = $day->addDay()) {
                $events->put(
                    $day->toDateString(),
                    $events->get($day->toDateString(), collect())->push($event)
                );
            }
        }

        $gridStart = $monthStart->startOfWeek(CarbonImmutable::SUNDAY);
        $gridEnd = $monthEnd->endOfWeek(CarbonImmutable::SATURDAY);
        $weeks = [];

        for ($week = $gridStart; $week <= $gridEnd; $week = $week->addWeek()) {
            $days = [];
            for ($day = $week; $day <= $week->addDays(6); $day = $day->addDay()) {
                $days[] = [
                    'date' => $day,
                    'events' => $events->get($day->toDateString(), collect()),
                ];
            }
            $weeks[] = $days;
        }

        return view('calendar.index', [
            'month' => $month,
            'weeks' => $weeks,
            'today' => $today,
            'canManage' => $request->user()->canManageCalendar(),
        ]);
    }

    public function create(Request $request): View
    {
        $month = $this->validatedMonth($request);

        return view('calendar.create', [
            'event' => new LibraryCalendarEvent,
            'month' => $month,
            'minimumStart' => CarbonImmutable::today()->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEvent($request);
        LibraryCalendarEvent::create($validated);

        return redirect()->route('calendar.index', ['month' => substr($validated['starts_at'], 0, 7)])
            ->with('status', 'Calendar item added.');
    }

    public function edit(Request $request, LibraryCalendarEvent $event): View
    {
        return view('calendar.edit', [
            'event' => $event,
            'month' => $this->validatedMonth($request, $event->starts_at->format('Y-m')),
            'minimumStart' => $event->starts_at->lt(CarbonImmutable::today())
                ? $event->starts_at->format('Y-m-d\TH:i')
                : CarbonImmutable::today()->format('Y-m-d\TH:i'),
        ]);
    }

    public function update(Request $request, LibraryCalendarEvent $event): RedirectResponse
    {
        $validated = $this->validateEvent($request, $event);
        $event->update($validated);

        return redirect()->route('calendar.index', ['month' => substr($validated['starts_at'], 0, 7)])
            ->with('status', 'Calendar item updated.');
    }

    public function destroy(LibraryCalendarEvent $event): RedirectResponse
    {
        $month = $event->starts_at->format('Y-m');
        $event->delete();

        return redirect()->route('calendar.index', ['month' => $month])
            ->with('status', 'Calendar item removed.');
    }

    private function validateEvent(Request $request, ?LibraryCalendarEvent $event = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['event', 'holiday', 'schedule'])],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_all_day' => ['nullable', 'boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
        $startsAt = CarbonImmutable::parse($validated['starts_at']);
        if ($startsAt->lt(CarbonImmutable::today()) && ! ($event && $startsAt->equalTo($event->starts_at))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'starts_at' => 'The start date must be today or later.',
            ]);
        }
        $validated['is_all_day'] = $request->boolean('is_all_day');

        return $validated;
    }

    private function validatedMonth(Request $request, ?string $default = null): string
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        return $validated['month'] ?? $default ?? now()->format('Y-m');
    }
}
