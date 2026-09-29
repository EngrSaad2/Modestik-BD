@extends('layouts.admin')

@section('title', 'Review Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
    <li class="breadcrumb-item active">Review Details</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Review Details</h4>
    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="form-card p-4 bg-white shadow-sm rounded">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Review Information</h5>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 35%;">Customer</th>
                    <td>{{ $review->user->name ?? 'Unknown' }} ({{ $review->user->email ?? '-' }})</td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td>
                        @if($review->product)
                            {{ $review->product->name }}
                        @else
                            <span class="text-muted">Deleted Product</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Rating</th>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td>{{ $review->comment }}</td>
                </tr>
                <tr>
                    <th>Featured on Home</th>
                    <td>
                        @if($review->is_featured)
                            <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Featured</span>
                        @else
                            <span class="text-muted">No</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $review->status === 'approved' ? 'bg-success' : ($review->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Submitted At</th>
                    <td>{{ $review->created_at->format('d F Y, h:i A') }}</td>
                </tr>
            </table>

            @if($review->images && is_array($review->images) && count($review->images) > 0)
                <div class="mt-4">
                    <h6 class="fw-bold mb-2">Uploaded Images:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($review->images as $img)
                            <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                <img src="{{ asset('storage/' . $img) }}" alt="Review Image" class="img-thumbnail rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-card p-4 bg-white shadow-sm rounded">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Moderate Review</h5>
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Review Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $review->status === 'rejected' ? 'selected' : '' }}>Rejected / Hidden</option>
                    </select>
                </div>
                
                <div class="mb-4 form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedSwitch" value="1" {{ $review->is_featured ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="isFeaturedSwitch">Feature this review on the homepage</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">Save Moderation Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
