@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-3">Submit a New Proposal</h2>

        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('proposals.store') }}" method="POST" enctype="multipart/form-data" class="compact-entry-form w-100 p-3 shadow rounded bg-light">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Proposal Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Timeline</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="timeline_from" class="form-label">From</label>
                        <input type="date" id="timeline_from" name="timeline_from" class="form-control"
                               value="{{ old('timeline_from') }}" required>
                        @error('timeline_from') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="timeline_to" class="form-label">To</label>
                        <input type="date" id="timeline_to" name="timeline_to" class="form-control"
                               value="{{ old('timeline_to') }}" required>
                        @error('timeline_to') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @error('timeline') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="destination" class="form-label">Where to Submit</label>
                <input type="text" id="destination" name="destination" class="form-control"
                       value="{{ old('destination') }}" maxlength="2048"
                       placeholder="Submission office, email address, or URL" required>
                @error('destination') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Proposal Details</label>
                <textarea name="details" class="form-control" rows="3" required>{{ old('details') }}</textarea>
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

    <script>
        const timelineFrom = document.getElementById('timeline_from');
        const timelineTo = document.getElementById('timeline_to');

        function updateTimelineLimits() {
            timelineTo.min = timelineFrom.value;
            timelineFrom.max = timelineTo.value;
        }

        timelineFrom.addEventListener('change', updateTimelineLimits);
        timelineTo.addEventListener('change', updateTimelineLimits);
        updateTimelineLimits();
    </script>
@endsection
