@extends('layouts.admin')

@section('title', 'Edit Tax')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.taxes.index') }}">Taxes</a></li>
    <li class="breadcrumb-item active">Edit Tax</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Tax Rate</h4>
    <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.taxes.update', $tax) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $tax->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tax Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="percentage" {{ $tax->type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ $tax->type == 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tax Rate / Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="rate" class="form-control" value="{{ $tax->rate }}" required>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $tax->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Tax Rate</button>
            </div>
        </div>
    </form>
</div>
@endsection
