@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-4">Record an Academic Meeting</h1>
        @include('admin.meetings.partials.form', [
            'action' => route('academics.meetings.store'),
            'method' => 'POST',
            'meeting' => null,
            'routePrefix' => 'academics.meetings',
        ])
    </div>
@endsection
