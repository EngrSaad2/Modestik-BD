@extends('layouts.frontend')

@section('title', 'Login - Modestik')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p>Enter your credentials to login to your dashboard account.</p>

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0" style="font-size: 13px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="name@example.com">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size:13px;">Password</label>
                        <a href="{{ route('password.request') }}" style="font-size:12px;">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label text-muted" style="font-size:13px;" for="remember">Keep me logged in</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                <span class="text-muted" style="font-size:13px;">Don't have an account?</span>
                <a href="{{ route('register') }}" class="fw-bold ms-1" style="font-size:13px;">Create Account</a>
            </div>
        </div>
    </div>
</section>
@endsection
