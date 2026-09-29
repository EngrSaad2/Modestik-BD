@extends('layouts.admin')

@section('title', 'Add Banner')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
    <li class="breadcrumb-item active">Add Banner</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Banner</h4>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Summer Sale Banner">
            </div>
            <div class="col-md-6">
                <label class="form-label">Position / Section</label>
                <select name="position" class="form-select" required>
                    <option value="home" selected>Home Page</option>
                    <option value="sidebar">Sidebar</option>
                    <option value="shop">Shop Page</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Banner Image <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Redirect URL</label>
                <input type="url" name="url" class="form-control" placeholder="https://example.com/collections/summer">
            </div>
            <div class="col-md-6">
                <label class="form-label">Order</label>
                <input type="number" name="order" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Banner</button>
            </div>
        </div>
    </form>
</div>
@endsection
