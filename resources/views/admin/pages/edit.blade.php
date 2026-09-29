@extends('layouts.admin')

@section('title', 'Edit Page')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Pages</a></li>
    <li class="breadcrumb-item active">Edit Page</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Page</h4>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Page Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control">
                @if($page->featured_image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $page->featured_image) }}" alt="current image" style="height: 50px; border-radius: 4px;">
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <label class="form-label">Page Content <span class="text-danger">*</span></label>
                <textarea name="content" rows="10" class="form-control" required>{{ $page->content }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Title (SEO)</label>
                <input type="text" name="meta_title" class="form-control" value="{{ $page->meta_title }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description (SEO)</label>
                <input type="text" name="meta_description" class="form-control" value="{{ $page->meta_description }}">
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $page->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Page</button>
            </div>
        </div>
    </form>
</div>
@endsection
