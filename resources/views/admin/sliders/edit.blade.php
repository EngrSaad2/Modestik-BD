@extends('layouts.admin')

@section('title', 'Edit Slider')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
    <li class="breadcrumb-item active">Edit Slider</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Slider</h4>
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $slider->title }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="{{ $slider->subtitle }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ $slider->description }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slider Image</label>
                <input type="file" name="image" class="form-control">
                @if($slider->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $slider->image) }}" alt="current image" style="height: 60px; border-radius: 4px;">
                    </div>
                @endif
            </div>
            <div class="col-md-3">
                <label class="form-label">Button Text</label>
                <input type="text" name="button_text" class="form-control" value="{{ $slider->button_text }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Button URL</label>
                <input type="url" name="button_url" class="form-control" value="{{ $slider->button_url }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Order</label>
                <input type="number" name="order" class="form-control" value="{{ $slider->order }}">
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $slider->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Slider</button>
            </div>
        </div>
    </form>
</div>
@endsection
