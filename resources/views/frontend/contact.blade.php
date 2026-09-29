@extends('layouts.frontend')

@section('title', 'Contact Us - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <h4 class="fw-bold mb-4">Contact Info</h4>
                    <p class="text-muted">Feel free to contact us with any questions or inquiries. We are here to help!</p>
                    
                    <div class="d-flex align-items-start mb-4 mt-4">
                        <i class="fas fa-map-marker-alt text-primary me-3 mt-1" style="font-size:20px;"></i>
                        <div>
                            <strong class="d-block mb-1">Our Store Address</strong>
                            <span class="text-muted">Dhaka, Bangladesh</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="fas fa-phone-alt text-primary me-3 mt-1" style="font-size:20px;"></i>
                        <div>
                            <strong class="d-block mb-1">Call Us Directly</strong>
                            <span class="text-muted">+880 1XXX-XXXXXX</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="fas fa-envelope text-primary me-3 mt-1" style="font-size:20px;"></i>
                        <div>
                            <strong class="d-block mb-1">Email Inquiry</strong>
                            <span class="text-muted">admin@modestikbd.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-lg-7">
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <h4 class="fw-bold mb-4">Get In Touch</h4>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">Subject</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size:13px;">Your Message <span class="text-danger">*</span></label>
                                <textarea name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Submit Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
