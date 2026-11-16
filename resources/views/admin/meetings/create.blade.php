@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-4">Record Meeting Minutes</h1>
        @include('admin.meetings.partials.form', [
            'action' => route('admin.meetings.store'),
            'method' => 'POST',
            'meeting' => null,
            'routePrefix' => 'admin.meetings',
        ])
    </div>
@endsection
