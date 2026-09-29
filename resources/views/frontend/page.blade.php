@extends('layouts.frontend')

@section('title', $page->meta_title ?? ($page->title . ' - Modestik'))
@section('meta_description', $page->meta_description)

@section('styles')
<style>
    .story-hero {
        background: linear-gradient(135deg, #fff5f6 0%, #ffebee 100%);
        padding: 60px 0;
        border-radius: 20px;
        margin-bottom: 40px;
        border: 1px solid #ffe4e6;
    }
    .story-title {
        color: var(--primary-dark, #be123c);
        font-family: 'Outfit', sans-serif;
    }
    .story-card {
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }
    .story-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        border-color: #ffe4e6;
    }
    .amanah-section {
        background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
        color: white;
        border-radius: 16px;
        padding: 40px;
        margin: 40px 0;
        position: relative;
        overflow: hidden;
    }
    .amanah-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }
    .icon-box {
        width: 50px;
        height: 50px;
        background: #fff5f6;
        color: #be123c;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 20px;
    }
    .text-justify {
        text-align: justify;
    }
</style>
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        @if($page->slug === 'about-us')
            <!-- Top Banner Image (Placed Before Title Section) -->
            <div class="row justify-content-center mb-4" data-aos="fade-up">
                <div class="col-lg-10">
                    <div class="position-relative overflow-hidden rounded-3 shadow-sm mx-auto" style="border: 1px solid #ffe4e6; background: #fff; max-width: 900px;">
                        <img src="{{ asset('images/about_top_banner2.png') }}" alt="Modestik Journey Header" class="w-100 img-fluid object-fit-cover" style="display: block; width: 100%; height: auto;">
                    </div>
                </div>
            </div>

            <!-- Custom Premium Our Story Layout -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h1 class="display-5 fw-bold story-title mt-2 mb-3" style="font-size: 32px; background: none; border: none; padding: 0;">The Journey of Modestik</h1>
            </div>

            <div class="row g-4 mb-5">
                <!-- Amanah Section -->
                <div class="col-12" data-aos="fade-up">
                    <div class="amanah-section shadow">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <h3 class="fw-bold mb-3"><i class="fas fa-hand-holding-heart me-2"></i>Business as an Amanah</h3>
                                <p class="mb-0 fs-6 lh-base text-justify">
                                    For Modestik, business is an Amanah (trust). Profit is important, but purpose is greater. That is why 2–3% of the monthly profits are dedicated to support underprivileged people across Bangladesh. Every purchase from Modestik becomes a small act of kindness, helping us bring hope to those who need it most. Modestik believes that when a community grows together, true success is shared.
                                </p>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="bg-white text-primary rounded-pill py-3 px-4 d-inline-block shadow-sm">
                                    <span class="fs-4 fw-bold">2% – 3%</span>
                                    <span class="d-block text-muted fw-bold" style="font-size: 12px;">Monthly Profit Donated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3 Pillars Section -->
                <!-- Pillar 1: How We Began -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white rounded-3 shadow-sm p-4 h-100 story-card">
                        <div class="icon-box">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">How We Began</h4>
                        <p class="text-muted text-justify" style="font-size: 14px; line-height: 1.7;">
                            Modestik started with carefully selected organic mehdi, premium mehdi stickers, and natural beauty essentials that are skin-safe, chemical-free, and thoughtfully sourced. Every product is chosen with care to ensure it reflects the brands commitment to purity, quality, and trust. As it grows Modestik aims to expand into modest fashion, jewelry, and other halal lifestyle essentials, bringing everything under one trusted name.
                        </p>
                    </div>
                </div>

                <!-- Pillar 2: Our Mission -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white rounded-3 shadow-sm p-4 h-100 story-card">
                        <div class="icon-box">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Our Mission</h4>
                        <p class="text-muted text-justify" style="font-size: 14px; line-height: 1.7;">
                            The mission of Modestik is to inspire a lifestyle where faith, modesty, and excellence come together. Modestik is committed to offer products that people can use with confidence. knowing they are natural, halal-focused, and carefully selected with their well-being in mind. It strive to build a long lasting relationships with the customers through transparency, consistency, and exceptional service.
                        </p>
                    </div>
                </div>

                <!-- Pillar 3: Our Vision -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white rounded-3 shadow-sm p-4 h-100 story-card">
                        <div class="icon-box">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Our Vision</h4>
                        <p class="text-muted text-justify" style="font-size: 14px; line-height: 1.7;">
                            The vision of Modestik is to reache far beyond borders. We dream of seeing Modestik become one of the world's most respected Muslim brands recognized not only for premium products but also for meaningful impact, ethical values, and sincere service to humanity.
                        </p>
                    </div>
                </div>

                <!-- Intro Card -->
                <div class="col-12" data-aos="fade-up">
                    <div class="bg-white rounded-3 shadow-sm p-4 p-md-5 border-start border-primary border-4">
                        <p class="fs-5 text-dark mb-0 lh-base text-justify" style="font-style: italic; font-weight: 500;">
                            "At Modestik, we believe a brand should stand for something greater than products, It should stand for purpose. The journey began with a simple vision: to create a trusted Muslim lifestyle brand that serves both the Ummah and humanity through honesty, quality, and compassion."
                        </p>
                    </div>
                </div>

                <!-- Closing Message -->
                <div class="col-12" data-aos="fade-up">
                    <div class="bg-white rounded-3 shadow-sm p-4 p-md-5 text-center">
                        <h4 class="fw-bold mb-3 text-dark">Building More Than a Brand</h4>
                        <p class="text-muted mx-auto mb-4" style="max-width: 700px;">
                            This is only the beginning of the journey. Every order, every customer, and every prayer encourages the team to move one step closer to the dream. Together, we are building more than a brand We are building a community driven by faith, compassion, and purpose.
                        </p>
                        <div class="p-3 bg-light rounded-pill d-inline-block px-5 mb-4">
                            <strong class="text-primary fs-6">Welcome to Modestik—where modest living meets meaningful giving.</strong>
                        </div>
                        <div class="mt-2">
                            <h5 class="fw-bold text-dark mb-1">Thank you</h5>
                            <p class="text-primary fw-bold">Team Modestik 💕</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Standard Page Layout -->
            <div class="bg-white rounded-3 shadow-sm p-4 p-md-5 max-width-900 mx-auto">
                <h1 class="fw-bold mb-4">{{ $page->title }}</h1>
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
