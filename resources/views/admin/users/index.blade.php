@extends('layouts.admin')

@section('title', 'Users')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Users</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add User</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage System Users & Staff</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.users.bulk-delete') }}" method="POST" id="bulkUserForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkUserDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="userSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllUsers">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $isProtected = ($user->id === 1 || $user->id === auth()->id());
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if(!$isProtected)
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                    @else
                                        <i class="fas fa-lock text-muted" title="Protected account" style="font-size: 11px;"></i>
                                    @endif
                                </td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        @if(!$isProtected)
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete user {{ addslashes($user->name) }}?')) document.getElementById('delete-user-{{ $user->id }}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($users as $user)
            @if($user->id !== 1 && $user->id !== auth()->id())
                <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        @endforeach

        <div class="p-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkUserDelete() {
    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one user to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected user(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllUsers');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const countText = document.getElementById('userSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (countText) countText.textContent = checkedCount + ' items selected';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && selectAll) selectAll.checked = false;
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.user-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
