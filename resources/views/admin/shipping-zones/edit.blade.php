@extends('layouts.admin')

@section('title', 'Edit Shipping Zone')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.shipping-zones.index') }}">Shipping Zones</a></li>
    <li class="breadcrumb-item active">Edit Shipping Zone</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Shipping Zone</h4>
    <a href="{{ route('admin.shipping-zones.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.shipping-zones.update', $shippingZone) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Zone Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $shippingZone->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Shipping Charge (৳) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="charge" class="form-control" value="{{ $shippingZone->charge }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Minimum Delivery Time (Days) <span class="text-danger">*</span></label>
                <input type="number" name="min_days" class="form-control" value="{{ $shippingZone->min_days }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Maximum Delivery Time (Days) <span class="text-danger">*</span></label>
                <input type="number" name="max_days" class="form-control" value="{{ $shippingZone->max_days }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Min order for Free Shipping (Optional)</label>
                <input type="number" step="0.01" name="free_shipping_min" class="form-control" value="{{ $shippingZone->free_shipping_min }}">
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ $shippingZone->status ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Update Shipping Zone</button>
            </div>
        </div>
    </form>
</div>
@endsection
