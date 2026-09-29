@extends('layouts.admin')

@section('title', 'Add Role')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Add Role</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Role</h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. editor" required>
            </div>
            
            <div class="col-md-12 mt-4">
                <label class="form-label fw-bold mb-3">Assign Permissions <span class="text-danger">*</span></label>
                <div class="row g-2">
                    @foreach($permissions as $perm)
                        <div class="col-md-3 col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}">
                                <label class="form-check-label text-capitalize" for="perm_{{ $perm->id }}">
                                    {{ str_replace('-', ' ', $perm->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Role</button>
            </div>
        </div>
    </form>
</div>
@endsection
