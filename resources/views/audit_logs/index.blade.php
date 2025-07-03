@extends('layouts.header')

@section('content')
    <style>
        .table {
            font-size: 0.875rem;
            white-space: nowrap; /* Prevent wrapping */
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            h1.mb-0 {
                font-size: 1.25rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .btn, .form-control, .form-select {
                font-size: 0.8rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
            }

            .card-footer {
                text-align: center;
            }
        }

        /* Ensure collapse details scroll horizontally */
        .collapse .card-body {
            overflow-x: auto;
        }
    </style>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Audit Logs</h1>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="bi bi-funnel"></i> Filters
            </button>
        </div>

        <!-- Filter Form - Collapsible -->
        <div class="card mb-4 collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter"></i> Apply
                        </button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex gap-2">
                <form action="{{ route('audit_logs.export') }}" method="GET">
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                    <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                    <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export CSV
                    </button>
                </form>
            </div>

            <div class="text-muted">
                Showing {{ $auditLogs->count() }} latest audit log{{ $auditLogs->count() !== 1 ? 's' : '' }}
            </div>
        </div>

        <!-- Audit Table -->
        @if($auditLogs->count() > 0)
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">User</th>
                            <th width="30%">Event</th>
                            <th width="15%">IP Address</th>
                            <th width="15%">Date & Time</th>
                            <th width="20%">Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($auditLogs as $log)
                            @php
                                $changes = is_array($log->changes) ? $log->changes : json_decode($log->changes, true);
                                $quantityDetails = $log->getQuantityDetails();
                                $eventType = str_replace('_', ' ', $log->action);
                            @endphp
                            <tr>
                                <td class="text-muted">#{{ $log->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($log->user)
                                            <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </span>
                                            {{ $log->user->name }}
                                        @else
                                            <span class="text-muted">Guest</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if(in_array($log->action, ['login', 'logout', 'failed_login']))
                                        <span class="badge bg-{{ $log->action == 'login' ? 'success' : ($log->action == 'logout' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $log->action == 'login' ? 'success' : ($log->action == 'logout' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($eventType) }}
                                            </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ ucfirst($eventType) }}
                                            </span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    <span class="d-block">{{ $log->created_at->format('d M Y') }}</span>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($quantityDetails)
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#details-{{ $log->id }}">
                                            Show Details
                                        </button>
                                        <div class="collapse mt-2" id="details-{{ $log->id }}">
                                            <div class="card card-body bg-light p-2">
                                                @if($quantityDetails['before'])
                                                    <div><strong>Before:</strong> {{ $quantityDetails['before'] }}</div>
                                                @endif
                                                @if($quantityDetails['after'])
                                                    <div><strong>After:</strong> {{ $quantityDetails['after'] }}</div>
                                                @endif
                                                @if(isset($quantityDetails['subtotal']))
                                                    <div><strong>Subtotal:</strong> {{ $quantityDetails['subtotal'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($changes)
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#details-{{ $log->id }}">
                                            Show Details
                                        </button>
                                        <div class="collapse mt-2" id="details-{{ $log->id }}">
                                            <div class="card card-body bg-light p-2">
                                                @foreach($changes as $key => $value)
                                                    @if(is_array($value))
                                                        <div><strong>{{ ucfirst($key) }}:</strong>
                                                            <ul class="mb-0 ps-3">
                                                                @foreach($value as $subKey => $subValue)
                                                                    <li>{{ $subKey }}: {{ $subValue }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <div><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white text-center text-muted">
                    Displaying {{ $auditLogs->count() }} audit log{{ $auditLogs->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h4 class="mt-3">No audit logs found</h4>
                    <p class="text-muted">Try adjusting your filters or check back later</p>
                </div>
            </div>
        @endif
    </div>
@endsection
