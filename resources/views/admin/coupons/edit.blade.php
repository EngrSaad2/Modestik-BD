@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item active">Edit Coupon</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Coupon</h4>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="percentage" {{ $coupon->type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Min Order Amount</label>
                <input type="number" step="0.01" name="min_order" class="form-control" value="{{ $coupon->min_order }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Max Discount Amount (For percentage type)</label>
                <input type="number" step="0.01" name="max_discount" class="form-control" value="{{ $coupon->max_discount }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Usage Limit (Per Coupon)</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Per User Limit</label>
                <input type="number" name="per_user_limit" class="form-control" value="{{ $coupon->per_user_limit }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expires_at" class="form-control" value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
            </div>
            <div class="col-md-12">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $coupon->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Coupon</button>
            </div>
        </div>
    </form>
</div>
@endsection
