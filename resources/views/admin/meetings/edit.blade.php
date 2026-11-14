@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-4">Edit Meeting</h1>
        @include('admin.meetings.partials.form', [
            'action' => route('admin.meetings.update', $meeting),
            'method' => 'PUT',
            'meeting' => $meeting,
            'routePrefix' => 'admin.meetings',
        ])
    </div>
@endsection
