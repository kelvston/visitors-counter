@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">Submit a New Proposal</h2>

        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('proposals.store') }}" method="POST" enctype="multipart/form-data" class="p-4 shadow rounded bg-light">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Proposal Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="timeline" class="form-label">Timeline</label>
                <input type="text" name="timeline" class="form-control" value="{{ old('timeline') }}" required>
                @error('timeline') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Proposal Details</label>
                <textarea name="details" class="form-control" rows="4" required>{{ old('details') }}</textarea>
                @error('details') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="attachment" class="form-label">PDF Attachment</label>
                <input type="file" name="attachment" class="form-control" accept="application/pdf" required>
                @error('attachment') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success px-4">Submit Proposal</button>
            </div>
        </form>
    </div>
@endsection
