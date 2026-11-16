@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-3">Add Library Calendar Item</h1>
        @include('calendar.partials.form', [
            'action' => route('calendar.store'),
            'method' => 'POST',
            'event' => $event,
            'month' => $month,
        ])
    </div>
@endsection
