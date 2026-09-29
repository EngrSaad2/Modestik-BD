@extends('layouts.frontend')

@section('title', ($blog->meta_title ?? $blog->title) . ' - Modestik')
@section('meta_description', $blog->meta_description ?? Str::limit(strip_tags($blog->content), 150))

@section('content')
<div class="page-breadcrumb" style="background: #f8f9fa; padding: 15px 0; border-bottom: 1px solid #e9ecef; margin-bottom: 30px;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="padding: 0; background: none; display: flex; gap: 8px; list-style: none;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: var(--primary); text-decoration: none;">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blogs.index') }}" style="color: var(--primary); text-decoration: none;"><span style="margin: 0 8px; color: #6c757d;">/</span> Blog</a></li>
                <li class="breadcrumb-item active" style="color: #6c757d;" aria-current="page"><span style="margin: 0 8px; color: #6c757d;">/</span> Detail</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: Blog Content -->
            <div class="col-lg-8">
                <article class="bg-white p-4 p-md-5 rounded-3 shadow-sm border" style="background: #fff; border: 1px solid #f1f5f9; border-radius: 8px;">
                    @if($blog->category)
                        <span class="badge bg-primary mb-3" style="background: var(--primary); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            {{ $blog->category->name }}
                        </span>
                    @endif
                    
                    <h1 class="fw-bold text-dark mb-3" style="font-size: 32px; line-height: 1.3;">{{ $blog->title }}</h1>
                    
                    <div class="text-muted mb-4 pb-3 border-bottom d-flex flex-wrap align-items-center gap-3" style="font-size: 13px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 15px;">
                        <span><i class="far fa-calendar-alt me-1"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}</span>
                        <span><i class="far fa-user me-1"></i> By Admin</span>
                        <span><i class="far fa-eye me-1"></i> {{ $blog->views }} views</span>
                    </div>

                    @if($blog->image_url)
                        <div class="mb-4 overflow-hidden rounded" style="border-radius: 8px;">
                            <img src="{{ $blog->image_url }}" class="img-fluid w-100 object-fit-cover" style="max-height: 450px; display: block;" alt="{{ $blog->title }}">
                        </div>
                    @endif

                    <div class="blog-post-content fs-6 lh-lg text-justify text-dark" style="color: #2d2325; font-size: 15px; line-height: 1.8;">
                        {!! $blog->content !!}
                    </div>
                </article>
            </div>

            <!-- Right Side: Sidebar -->
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-3 shadow-sm border mb-4" style="background: #fff; border: 1px solid #f1f5f9; border-radius: 8px;">
                    <h4 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Categories</h4>
                    <div class="d-flex flex-column gap-2" style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('blogs.index') }}" class="d-flex justify-content-between align-items-center py-1 text-dark" style="display: flex; justify-content: space-between; text-decoration: none; color: #2d2325; font-size: 14px;">
                            <span>All</span>
                            <span class="badge bg-light text-dark rounded-pill" style="background: #f8f9fa; padding: 4px 8px; border-radius: 20px; font-size: 11px;">{{ \App\Models\Blog::published()->count() }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('blogs.index', ['category' => $cat->slug]) }}" class="d-flex justify-content-between align-items-center py-1 text-dark" style="display: flex; justify-content: space-between; text-decoration: none; color: #2d2325; font-size: 14px; {{ $blog->category_id === $cat->id ? 'font-weight: 700; color: var(--primary) !important;' : '' }}">
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
