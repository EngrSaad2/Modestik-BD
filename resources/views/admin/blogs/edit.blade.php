@extends('layouts.admin')

@section('title', 'Edit Blog Post')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
    <li class="breadcrumb-item active">Edit Blog Post</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Blog Post</h4>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ $blog->title }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Blog Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="image" class="form-control">
                @if($blog->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $blog->image) }}" alt="current image" style="height: 50px; border-radius: 4px;">
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Excerpt / Short Summary</label>
                <input type="text" name="excerpt" class="form-control" value="{{ $blog->excerpt }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Full Content <span class="text-danger">*</span></label>
                <textarea name="content" rows="12" class="form-control" required>{{ $blog->content }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Title (SEO)</label>
                <input type="text" name="meta_title" class="form-control" value="{{ $blog->meta_title }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description (SEO)</label>
                <input type="text" name="meta_description" class="form-control" value="{{ $blog->meta_description }}">
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $blog->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Published Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Post</button>
            </div>
        </div>
    </form>
</div>
@endsection
