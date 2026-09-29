@extends('layouts.admin')

@section('title', 'Add Tax')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.taxes.index') }}">Taxes</a></li>
    <li class="breadcrumb-item active">Add Tax</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Tax Rate</h4>
    <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.taxes.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. VAT 15%" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tax Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="percentage" selected>Percentage (%)</option>
                    <option value="fixed">Fixed Amount (৳)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tax Rate / Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="rate" class="form-control" placeholder="e.g. 15 or 50" required>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Tax Rate</button>
            </div>
        </div>
    </form>
</div>
@endsection
