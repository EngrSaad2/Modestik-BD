@extends('layouts.admin')

@section('title', 'Roles')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Roles</h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Role</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage System User Roles</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td><strong>{{ ucfirst($role->name) }}</strong></td>
                            <td>
                                @foreach($role->permissions as $perm)
                                    <span class="badge bg-light text-dark border mb-1" style="font-size:10px;">{{ str_replace('-', ' ', $perm->name) }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    @if($role->name !== 'admin')
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $roles->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
