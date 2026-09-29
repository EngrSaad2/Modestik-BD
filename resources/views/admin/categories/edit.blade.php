@extends('layouts.admin')

@section('title', 'Edit Category')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">Edit Category</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Category</h4>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category Name</label>
                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-select select2">
                    <option value="">None (Make Parent)</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ $category->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Icon HTML</label>
                <input type="text" name="icon" class="form-control" value="{{ $category->icon }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">SEO Friendly URL Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
            </div>
            <div class="col-md-3 mt-4">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $category->is_featured ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured Category</label>
                </div>
            </div>
            <div class="col-md-3 mt-4">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $category->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>

            <!-- SEO Panel -->
            <div class="col-12 border-top pt-4 mt-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-search me-2 text-muted"></i>Category Search Engine Optimization (SEO)</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $category->meta_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" class="form-control" placeholder="Comma separated keywords...">{{ old('meta_keywords', $category->meta_keywords) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Category</button>
            </div>
        </div>
    </form>
</div>
@endsection
