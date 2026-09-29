@extends('layouts.frontend')

@section('title', 'Forgot Password - Modestik')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2>Forgot Password?</h2>
            <p>Enter your registered email address below, and we will email you password reset instructions.</p>

            @if(session('success'))
                <div class="alert alert-success py-2">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0" style="font-size: 13px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size:13px;">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="name@example.com">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Send Reset Link</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="fw-bold text-muted" style="font-size:13px;"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
            </div>
        </div>
    </div>
</section>
@endsection
