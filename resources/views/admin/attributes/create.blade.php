@extends('layouts.admin')

@section('title', 'Add Attribute')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}">Attributes</a></li>
    <li class="breadcrumb-item active">Add Attribute</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Attribute</h4>
    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.attributes.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Attribute Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Color" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="text" selected>Text</option>
                    <option value="color">Color</option>
                    <option value="size">Size</option>
                </select>
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Attribute</button>
            </div>
        </div>
    </form>
</div>
@endsection
