@extends('layouts.admin')

@section('title', 'Add Slider')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
    <li class="breadcrumb-item active">Add Slider</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Slider</h4>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. New Arrivals">
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle</label>
                <input type="text" name="subtitle" class="form-control" placeholder="e.g. Up to 50% Off">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Slider description text..."></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slider Image <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Button Text</label>
                <input type="text" name="button_text" class="form-control" placeholder="e.g. Shop Now">
            </div>
            <div class="col-md-3">
                <label class="form-label">Button URL</label>
                <input type="url" name="button_url" class="form-control" placeholder="https://example.com/shop">
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
                <button type="submit" class="btn btn-admin-primary px-4">Save Slider</button>
            </div>
        </div>
    </form>
</div>
@endsection
