@if($proposals->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Timeline</th>
                <th>Where to Submit</th>
                <th>Details</th>
                <th>PDF</th>
                <th>Submitted At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->title }}</td>
                    <td>
                        @if($proposal->timeline_from && $proposal->timeline_to)
                            {{ $proposal->timeline_from->format('d M Y') }} – {{ $proposal->timeline_to->format('d M Y') }}
                        @else
                            {{ $proposal->timeline }}
                        @endif
                    </td>
                    <td>
                        @if(filter_var($proposal->destination, FILTER_VALIDATE_URL))
                            <a href="{{ $proposal->destination }}" target="_blank" rel="noopener noreferrer">
                                {{ $proposal->destination }}
                            </a>
                        @else
                            {{ $proposal->destination ?: '—' }}
                        @endif
                    </td>
                    <td>{{ Str::limit($proposal->details, 50) }}</td>
                    <td>
                        @if($proposal->attachment)
                            <a href="{{ route('proposals.download', $proposal) }}" class="btn btn-sm btn-primary">
                                Download
                            </a>
                        @else
                            <span class="text-muted">No file</span>
                        @endif
                    </td>
                    <td>{{ $proposal->created_at->format('d M Y, H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $proposals->links() }}
    </div>
@else
    <p class="text-center text-muted">No proposals found.</p>
@endif
