@if($proposals->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Timeline</th>
                <th>Details</th>
                <th>PDF</th>
                <th>Submitted At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->title }}</td>
                    <td>{{ $proposal->timeline }}</td>
                    <td>{{ Str::limit($proposal->details, 50) }}</td>
                    <td>
                        @if($proposal->attachment_url)
                            <a href="{{ $proposal->attachment_url }}" target="_blank" class="btn btn-sm btn-primary">
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
