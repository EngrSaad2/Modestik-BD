@extends('layouts.admin')

@section('title', 'Campaigns')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Campaigns</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Campaigns</h4>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Campaign</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Marketing Campaigns</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Discount</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                @if($campaign->image)
                                    <img src="{{ asset('storage/' . $campaign->image) }}" alt="campaign" class="image-preview" style="height: 50px; width: 80px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td><strong>{{ $campaign->name }}</strong></td>
                            <td>
                                {{ $campaign->discount_type === 'percentage' ? $campaign->discount_value . '%' : '৳' . number_format($campaign->discount_value, 2) }}
                            </td>
                            <td>{{ $campaign->starts_at ? $campaign->starts_at->format('d M Y') : '-' }}</td>
                            <td>{{ $campaign->ends_at ? $campaign->ends_at->format('d M Y') : '-' }}</td>
                            <td><span class="badge bg-info">{{ $campaign->products->count() }} items</span></td>
                            <td>
                                <span class="badge {{ $campaign->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $campaign->is_active ? 'Active' : 'Inactive/Expired' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No campaigns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $campaigns->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
