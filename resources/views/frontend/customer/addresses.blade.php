@extends('layouts.frontend')

@section('title', 'Saved Addresses - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Addresses</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Panel -->
            <div class="col-lg-3">
                <div class="bg-white rounded-3 shadow-sm p-3">
                    <div class="text-center py-3 border-bottom mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width:60px;height:60px;font-size:22px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ auth()->user()->name }}</h6>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush" style="font-size: 14px;">
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        <a href="{{ route('customer.wishlist') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-heart me-2"></i>Wishlist</a>
                        <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action border-0 py-2.5 fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Addresses</a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-user me-2"></i>Profile Update</a>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Saved Addresses</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal"><i class="fas fa-plus me-1"></i>Add New Address</button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-3">
                    @forelse($addresses as $addr)
                        <div class="col-md-6">
                            <div class="bg-white rounded-3 shadow-sm p-4 h-100 position-relative border {{ $addr->is_default ? 'border-primary' : '' }}">
                                @if($addr->is_default)
                                    <span class="badge bg-primary position-absolute" style="top: 15px; right: 15px;">Default</span>
                                @endif
                                <h6 class="fw-bold mb-2">{{ $addr->label }}</h6>
                                <strong class="d-block mb-1" style="font-size: 14px;">{{ $addr->name }}</strong>
                                <small class="text-muted d-block mb-2">Phone: {{ $addr->phone }}</small>
                                <p class="text-muted mb-4" style="font-size: 13px;">
                                    {{ $addr->address }}, {{ $addr->area ? $addr->area . ', ' : '' }}{{ $addr->district }}, {{ $addr->division }} {{ $addr->zip }}
                                </p>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal-{{ $addr->id }}">Edit</button>
                                    <form action="{{ route('customer.addresses.delete', $addr->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this address?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Address Modal -->
                        <div class="modal fade" id="editAddressModal-{{ $addr->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('customer.addresses.update', $addr->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Address</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Label (e.g. Home, Office)</label>
                                                <input type="text" name="label" class="form-control" value="{{ $addr->label }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Receiver's Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $addr->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Contact Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $addr->phone }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label fw-bold">Division</label>
                                                    <input type="text" name="division" class="form-control" value="{{ $addr->division }}" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-bold">District</label>
                                                    <input type="text" name="district" class="form-control" value="{{ $addr->district }}" required>
                                                </div>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label fw-bold">Area</label>
                                                    <input type="text" name="area" class="form-control" value="{{ $addr->area }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-bold">ZIP Code</label>
                                                    <input type="text" name="zip" class="form-control" value="{{ $addr->zip }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Delivery Address</label>
                                                <textarea name="address" rows="3" class="form-control" required>{{ $addr->address }}</textarea>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="edit-default-{{ $addr->id }}" {{ $addr->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label text-muted" for="edit-default-{{ $addr->id }}">Set as default address</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm">
                            <i class="fas fa-map-marker-alt text-muted mb-3" style="font-size:36px;"></i>
                            <p class="text-muted mb-0">No saved addresses yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Label (e.g. Home, Office)</label>
                        <input type="text" name="label" class="form-control" placeholder="e.g. Home" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Receiver's Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contact Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 017XXXXXXXX" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Division</label>
                            <input type="text" name="division" class="form-control" placeholder="e.g. Dhaka" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">District</label>
                            <input type="text" name="district" class="form-control" placeholder="e.g. Dhaka City" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Area</label>
                            <input type="text" name="area" class="form-control" placeholder="e.g. Dhanmondi">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">ZIP Code</label>
                            <input type="text" name="zip" class="form-control" placeholder="e.g. 1209">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="House, Road, Apartment details..." required></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="add-default">
                        <label class="form-check-label text-muted" for="add-default">Set as default address</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
