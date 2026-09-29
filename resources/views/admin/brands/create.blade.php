@extends('layouts.admin')

@section('title', 'Add Brand')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
    <li class="breadcrumb-item active">Add Brand</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Brand</h4>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-image">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Brand Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Samsung" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand Logo</label>
                <input type="file" name="logo" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">Website URL</label>
                <input type="url" name="website" class="form-control" placeholder="https://example.com">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Short description of the brand..."></textarea>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                    <label class="form-check-label" for="is_featured">Featured Brand</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Brand</button>
            </div>
        </div>
    </form>
</div>
@endsection
