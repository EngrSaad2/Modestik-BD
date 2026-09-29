@extends('layouts.admin')

@section('title', 'Add Campaign')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">Campaigns</a></li>
    <li class="breadcrumb-item active">Add Campaign</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Campaign</h4>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Eid Mega Offer" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                <select name="discount_type" class="form-select" required>
                    <option value="percentage" selected>Percentage (%)</option>
                    <option value="fixed">Fixed Amount (৳)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="discount_value" class="form-control" placeholder="e.g. 15 or 300" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Campaign Banner Image</label>
                <input type="file" name="image" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Starts At <span class="text-danger">*</span></label>
                <input type="datetime-local" name="starts_at" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ends At <span class="text-danger">*</span></label>
                <input type="datetime-local" name="ends_at" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">Select Campaign Products <span class="text-danger">*</span></label>
                <select name="products[]" class="form-select select2" multiple required style="width:100%;">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (৳{{ number_format($product->price) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Brief details about the campaign..."></textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                    <label class="form-check-label" for="status">Active Status</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Campaign</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Choose products...'
        });
    });
</script>
@endsection
