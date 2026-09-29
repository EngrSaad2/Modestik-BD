@extends('layouts.admin')

@section('title', 'Add Category')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">Add Category</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Category</h4>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Smart Devices" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Parent Category (Optional)</label>
                <select name="parent_id" class="form-select select2">
                    <option value="">None (Make Parent)</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Icon HTML (e.g. &lt;i class="fas fa-mobile-alt"&gt;&lt;/i&gt;)</label>
                <input type="text" name="icon" class="form-control" placeholder='<i class="fas fa-folder"></i>'>
            </div>
            <div class="col-md-6">
                <label class="form-label">SEO Friendly URL Slug</label>
                <input type="text" name="slug" class="form-control" placeholder="Auto generated if blank">
            </div>
            <div class="col-md-3 mt-4">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                    <label class="form-check-label" for="is_featured">Featured Category</label>
                </div>
            </div>
            <div class="col-md-3 mt-4">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>

            <!-- SEO Panel -->
            <div class="col-12 border-top pt-4 mt-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-search me-2 text-muted"></i>Category Search Engine Optimization (SEO)</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" placeholder="SEO Meta Title">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control" placeholder="SEO Meta Description..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" class="form-control" placeholder="Comma separated keywords (e.g. organic mehendi, halal beauty, nail powder)..."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Category</button>
            </div>
        </div>
    </form>
</div>
@endsection
