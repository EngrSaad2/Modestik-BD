@extends('layouts.frontend')

@section('title', 'Insights & Articles - Modestik')

@section('content')
<div class="page-breadcrumb" style="background: #f8f9fa; padding: 15px 0; border-bottom: 1px solid #e9ecef; margin-bottom: 30px;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="padding: 0; background: none; display: flex; gap: 8px; list-style: none;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: var(--primary); text-decoration: none;">Home</a></li>
                <li class="breadcrumb-item" style="color: #6c757d;"><span style="margin: 0 8px; color: #6c757d;">/</span> Blog</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section py-4">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-dark mb-2" style="font-size: 32px;">Our Blog & Insights</h1>
            <p class="text-muted mx-auto" style="max-width: 500px; font-size: 14px;">Learn tips, stories, and guides about organic Mehendi and modest beauty.</p>
        </div>

        <div class="row g-4">
            <!-- Left Side: Blog Posts -->
            <div class="col-lg-8">
                <div class="row g-4">
                    @forelse($blogs as $blog)
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden" style="background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f1f5f9 !important; transition: all 0.3s ease;">
                                <div class="position-relative">
                                    <a href="{{ route('blogs.show', $blog->slug) }}">
                                        <img src="{{ $blog->image_url ?? asset('images/about_top_banner2.png') }}" class="card-img-top w-100 object-fit-cover" style="height: 200px; display: block;" alt="{{ $blog->title }}">
                                    </a>
                                    @if($blog->category)
                                        <span class="position-absolute bg-primary text-white px-3 py-1 rounded-pill fw-semibold" style="position: absolute; top: 15px; right: 15px; font-size: 10px; background: var(--primary);">
                                            {{ $blog->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="text-muted mb-2" style="font-size: 12px; display: flex; align-items: center; gap: 8px;">
                                        <span><i class="far fa-calendar-alt me-1"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}</span>
                                        <span>|</span>
                                        <span><i class="far fa-eye me-1"></i> {{ $blog->views }} views</span>
                                    </div>
                                    <h3 class="card-title h5 fw-bold mb-3" style="font-size: 18px; line-height: 1.4;">
                                        <a href="{{ route('blogs.show', $blog->slug) }}" class="text-dark hover-primary" style="color: #2d2325; text-decoration: none;">{{ $blog->title }}</a>
                                    </h3>
                                    <p class="card-text text-muted mb-4" style="font-size: 13px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 4.8em;">
                                        {{ $blog->excerpt ?? Str::words(strip_tags($blog->content), 20) }}
                                    </p>
                                    <div class="mt-auto">
                                        <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-sm rounded-pill px-3" style="background: var(--primary-light); color: var(--primary); border: 1px solid var(--primary); font-size: 12px; font-weight: 600;">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="far fa-newspaper text-muted mb-3" style="font-size: 48px;"></i>
                            <h3 class="fw-bold text-muted">No blog posts found.</h3>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $blogs->links() }}
                </div>
            </div>

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-3 shadow-sm border mb-4" style="background: #fff; border: 1px solid #f1f5f9; border-radius: 8px;">
                    <h4 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Categories</h4>
                    <div class="d-flex flex-column gap-2" style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('blogs.index') }}" class="d-flex justify-content-between align-items-center py-1 text-dark" style="display: flex; justify-content: space-between; text-decoration: none; color: #2d2325; font-size: 14px; {{ !request('category') ? 'font-weight: 700; color: var(--primary) !important;' : '' }}">
                            <span>All</span>
                            <span class="badge bg-light text-dark rounded-pill" style="background: #f8f9fa; padding: 4px 8px; border-radius: 20px; font-size: 11px;">{{ \App\Models\Blog::published()->count() }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('blogs.index', ['category' => $cat->slug]) }}" class="d-flex justify-content-between align-items-center py-1 text-dark" style="display: flex; justify-content: space-between; text-decoration: none; color: #2d2325; font-size: 14px; {{ request('category') === $cat->slug ? 'font-weight: 700; color: var(--primary) !important;' : '' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="badge bg-light text-dark rounded-pill" style="background: #f8f9fa; padding: 4px 8px; border-radius: 20px; font-size: 11px;">{{ $cat->blogs_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-4 rounded-3 shadow-sm border" style="background: #fff; border: 1px solid #f1f5f9; border-radius: 8px;">
                    <h4 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Recent Posts</h4>
                    <div class="d-flex flex-column gap-3" style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($recentBlogs as $rb)
                            <div class="d-flex align-items-center gap-3" style="display: flex; gap: 12px; align-items: center;">
                                <a href="{{ route('blogs.show', $rb->slug) }}" class="flex-shrink-0" style="flex-shrink: 0;">
                                    <img src="{{ $rb->image_url ?? asset('images/about_top_banner2.png') }}" class="rounded object-fit-cover" style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover;" alt="{{ $rb->title }}">
                                </a>
                                <div>
                                    <h5 class="fw-semibold mb-1" style="font-size: 13px; line-height: 1.4; margin: 0 0 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <a href="{{ route('blogs.show', $rb->slug) }}" class="text-dark" style="color: #2d2325; text-decoration: none;">{{ $rb->title }}</a>
                                    </h5>
                                    <small class="text-muted" style="font-size: 11px; color: #6c757d;">{{ $rb->published_at ? $rb->published_at->format('M d, Y') : $rb->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
