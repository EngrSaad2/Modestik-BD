@extends('layouts.admin')

@section('title', 'Complaint Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.complaints.index') }}">Complaints</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back to Complaints</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="table-card p-4">
            <h5 class="fw-bold mb-4">Complaint Information</h5>
            
            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 12px;">Subject:</span>
                <strong class="fs-5">{{ $complaint->subject }}</strong>
            </div>

            <div class="mb-4">
                <span class="text-muted d-block" style="font-size: 12px;">Message:</span>
                <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">{{ $complaint->message }}</div>
            </div>

            <hr>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <span class="text-muted d-block">Customer Name:</span>
                    <strong>{{ $complaint->name }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Phone Number:</span>
                    <strong>{{ $complaint->phone }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Order ID Ref:</span>
                    <strong>{{ $complaint->order_number ?? 'None' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Submitted Date:</span>
                    <strong>{{ $complaint->created_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-card p-4">
            <h5 class="fw-bold mb-4">Manage Status</h5>
            
            <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Complaint Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
