@extends('layouts.admin')

@section('title', 'Add Blog Post')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
    <li class="breadcrumb-item active">Add Blog Post</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Blog Post</h4>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Benefits of Smartwatches" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Blog Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="image" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Excerpt / Short Summary</label>
                <input type="text" name="excerpt" class="form-control" placeholder="Short intro of the post...">
            </div>
            <div class="col-md-12">
                <label class="form-label">Full Content <span class="text-danger">*</span></label>
                <textarea name="content" rows="12" class="form-control" placeholder="Write full blog article..." required></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Title (SEO)</label>
                <input type="text" name="meta_title" class="form-control" placeholder="SEO Title">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description (SEO)</label>
                <input type="text" name="meta_description" class="form-control" placeholder="SEO Description">
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Publish Immediately</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Post</button>
            </div>
        </div>
    </form>
</div>
@endsection
