@extends('layouts.header')

@section('content')
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold">Proposals Pending Submission</h1>
            @if ($canManage)
                <a href="{{ route('proposals.create') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Submit Proposal
                </a>
            @endif
        </div>

        <form id="filterForm" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label for="searchBox" class="form-label">Search proposals</label>
                <input type="text" name="search" id="searchBox" class="form-control"
                       placeholder="Search by Title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="fromDate" class="form-label">Timeline overlaps from</label>
                <input type="date" name="from" id="fromDate" class="form-control"
                       value="{{ request('from') }}" max="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <label for="toDate" class="form-label">Timeline overlaps to</label>
                <input type="date" name="to" id="toDate" class="form-control"
                       value="{{ request('to') }}" min="{{ request('from') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('proposal') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
        <div id="filterError" class="alert alert-danger py-2 mb-3" role="alert" hidden></div>

        {{-- Table container --}}
        <div id="tableData">
            @include('proposals.partials.table', ['proposals' => $proposals])
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let searchBox = document.getElementById('searchBox');
            let fromDate  = document.getElementById('fromDate');
            let toDate    = document.getElementById('toDate');
            let tableData = document.getElementById('tableData');
            let filterError = document.getElementById('filterError');
            let filterForm = document.getElementById('filterForm');

            function updateDateLimits() {
                fromDate.max = toDate.value;
                toDate.min = fromDate.value;
            }

            function fetchData(page = 1) {
                updateDateLimits();

                let params = new URLSearchParams({
                    search: searchBox.value,
                    from: fromDate.value,
                    to: toDate.value,
                    page: page
                });

                fetch("{{ route('proposals.search') }}?" + params, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(async res => {
                        if (!res.ok) {
                            let data = await res.json();
                            throw new Error(Object.values(data.errors || {}).flat()[0] || 'Unable to filter proposals.');
                        }

                        return res.text();
                    })
                    .then(html => {
                        filterError.hidden = true;
                        tableData.innerHTML = html;

                        // attach click to pagination links inside returned HTML
                        tableData.querySelectorAll('.pagination a').forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                let url = new URL(this.href);
                                let page = url.searchParams.get('page');
                                fetchData(page);
                            });
                        });
                    })
                    .catch(error => {
                        filterError.textContent = error.message;
                        filterError.hidden = false;
                    });
            }

            filterForm.addEventListener('submit', function (event) {
                event.preventDefault();
                fetchData();
            });

            // live search typing
            searchBox.addEventListener('keyup', () => fetchData());
            fromDate.addEventListener('change', () => fetchData());
            toDate.addEventListener('change', () => fetchData());
        });
    </script>
@endpush
