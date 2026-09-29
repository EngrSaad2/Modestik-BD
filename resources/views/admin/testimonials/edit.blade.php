@extends('layouts.admin')

@section('title', 'Edit Testimonial')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
    <li class="breadcrumb-item active">Edit Testimonial</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Testimonial</h4>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Client Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $testimonial->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Designation / Title</label>
                <input type="text" name="designation" class="form-control" value="{{ $testimonial->designation }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Client Avatar Image</label>
                <input type="file" name="image" class="form-control">
                @if($testimonial->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $testimonial->image) }}" alt="current image" style="height: 50px; border-radius: 50%;">
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Rating <span class="text-danger">*</span></label>
                <select name="rating" class="form-select" required>
                    <option value="5" {{ $testimonial->rating == 5 ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ $testimonial->rating == 4 ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ $testimonial->rating == 3 ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ $testimonial->rating == 2 ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ $testimonial->rating == 1 ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Feedback / Review Content <span class="text-danger">*</span></label>
                <textarea name="content" rows="4" class="form-control" required>{{ $testimonial->content }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Order</label>
                <input type="number" name="order" class="form-control" value="{{ $testimonial->order }}">
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $testimonial->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Testimonial</button>
            </div>
        </div>
    </form>
</div>
@endsection
