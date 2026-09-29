@extends('layouts.admin')

@section('title', 'Edit Brand')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
    <li class="breadcrumb-item active">Edit Brand</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Brand</h4>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Brand Name</label>
                <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand Logo</label>
                <input type="file" name="logo" class="form-control">
                @if($brand->logo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="logo" class="image-preview">
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <label class="form-label">Website URL</label>
                <input type="url" name="website" class="form-control" value="{{ $brand->website }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ $brand->description }}</textarea>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $brand->is_featured ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured Brand</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $brand->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Brand</button>
            </div>
        </div>
    </form>
</div>
@endsection
