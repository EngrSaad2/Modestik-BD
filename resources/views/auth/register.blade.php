@extends('layouts.frontend')

@section('title', 'Register - Modestik')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-card" style="max-width: 500px;">
            <h2>Create Account</h2>
            <p>Sign up to shop faster, manage addresses, track orders and earn rewards.</p>

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0" style="font-size: 13px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">Full Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g. Rahim Ahmed">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="e.g. rahim@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" placeholder="e.g. 017XXXXXXXX">
                </div>
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size:13px;">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size:13px;">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign Up</button>
            </form>
            
            <div class="text-center mt-4">
                <span class="text-muted" style="font-size:13px;">Already have an account?</span>
                <a href="{{ route('login') }}" class="fw-bold ms-1" style="font-size:13px;">Login Now</a>
            </div>
        </div>
    </div>
</section>
@endsection
