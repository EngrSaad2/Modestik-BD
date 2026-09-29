@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item active">Add Coupon</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Coupon</h4>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" placeholder="e.g. SAVE20" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="percentage" selected>Percentage (%)</option>
                    <option value="fixed">Fixed Amount (৳)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="value" class="form-control" placeholder="e.g. 10 or 500" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Min Order Amount</label>
                <input type="number" step="0.01" name="min_order" class="form-control" placeholder="e.g. 1000">
            </div>
            <div class="col-md-6">
                <label class="form-label">Max Discount Amount (For percentage type)</label>
                <input type="number" step="0.01" name="max_discount" class="form-control" placeholder="e.g. 200">
            </div>
            <div class="col-md-6">
                <label class="form-label">Usage Limit (Per Coupon)</label>
                <input type="number" name="usage_limit" class="form-control" placeholder="e.g. 100">
            </div>
            <div class="col-md-6">
                <label class="form-label">Per User Limit</label>
                <input type="number" name="per_user_limit" class="form-control" value="1">
            </div>
            <div class="col-md-6">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expires_at" class="form-control">
            </div>
            <div class="col-md-12">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Coupon</button>
            </div>
        </div>
    </form>
</div>
@endsection
