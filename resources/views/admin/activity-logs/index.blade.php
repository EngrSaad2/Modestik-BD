@extends('layouts.admin')

@section('title', 'Activity Log')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Activity Log</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Activity Log</h4>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage System Security & Action Logs</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><strong>{{ $log->user->name ?? 'System/Guest' }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td><code>{{ $log->model ?? '-' }}</code></td>
                            <td>{{ $log->model_id ?? '-' }}</td>
                            <td><code>{{ $log->ip ?? '-' }}</code></td>
                            <td><small class="text-muted text-truncate d-block" style="max-width:250px;" title="{{ $log->user_agent }}">{{ $log->user_agent }}</small></td>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No activity logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
