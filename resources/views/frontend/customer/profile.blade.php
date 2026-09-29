@extends('layouts.frontend')

@section('title', 'Profile Update - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
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
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    <div class="list-group list-group-flush" style="font-size: 14px;">
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        <a href="{{ route('customer.wishlist') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-heart me-2"></i>Wishlist</a>
                        <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-map-marker-alt me-2"></i>Addresses</a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action border-0 py-2.5 fw-bold text-primary"><i class="fas fa-user me-2"></i>Profile Update</a>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="col-lg-9">
                <h5 class="fw-bold mb-4">Profile Update</h5>

                <div class="bg-white rounded-3 shadow-sm p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 13px;">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 13px;">Contact Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size: 13px;">Email Address</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                <small class="text-muted">Contact email address cannot be changed.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 13px;">New Password (Optional)</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 13px;">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
