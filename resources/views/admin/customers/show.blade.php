@extends('layouts.admin')

@section('title', 'Customer Details - ' . $customer->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to Customers</a>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Customer Profile Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body text-center">
                <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}" class="rounded-circle img-thumbnail mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                <p class="text-muted mb-3">{{ $customer->email }}</p>
                <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-danger' }} mb-2">
                    {{ ucfirst($customer->status) }}
                </span>
                <div class="text-start mt-4 pt-3 border-top">
                    <p class="mb-2"><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Registered At:</strong> {{ $customer->created_at->format('d M Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Addresses -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Addresses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($customer->addresses as $address)
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded @if($address->is_default) border-primary bg-light @endif">
                                @if($address->is_default)
                                    <span class="badge bg-primary float-end mb-2">Default</span>
                                @endif
                                <h6 class="fw-bold mb-2">{{ ucfirst($address->address_type ?? 'Address') }}</h6>
                                <p class="mb-1 text-muted">{{ $address->address }}</p>
                                <p class="mb-0 text-muted">
                                    {{ $address->area ? $address->area . ', ' : '' }}
                                    {{ $address->district }}, {{ $address->division }} 
                                    {{ $address->zip ? '- ' . $address->zip : '' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-3">No addresses found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Orders History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-shopping-bag me-2 text-primary"></i>Order History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>৳{{ number_format($order->total, 2) }}</td>
                                    <td>{!! $order->status_badge !!}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View Order Details"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No orders placed by this customer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
