@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-3">Edit Library Calendar Item</h1>
        @include('calendar.partials.form', [
            'action' => route('calendar.update', $event),
            'method' => 'PUT',
            'event' => $event,
            'month' => $month,
        ])
    </div>
@endsection
