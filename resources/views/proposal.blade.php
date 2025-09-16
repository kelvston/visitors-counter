@extends('layouts.header')

@section('content')
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold">Proposals Pending Submission</h1>
            <a href="{{ route('proposals.create') }}" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle"></i> Submit Proposal
            </a>
        </div>

        {{-- Search & Filter --}}
        <form id="filterForm" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" id="searchBox" class="form-control"
                       placeholder="Search by Title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="from" id="fromDate" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" id="toDate" class="form-control" value="{{ request('to') }}">
            </div>
        </form>

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

            function fetchData(page = 1) {
                let params = new URLSearchParams({
                    search: searchBox.value,
                    from: fromDate.value,
                    to: toDate.value,
                    page: page
                });

                fetch("{{ route('proposals.search') }}?" + params)
                    .then(res => res.text())
                    .then(html => {
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
                    });
            }

            // live search typing
            searchBox.addEventListener('keyup', () => fetchData());
            fromDate.addEventListener('change', () => fetchData());
            toDate.addEventListener('change', () => fetchData());
        });
    </script>
@endpush
