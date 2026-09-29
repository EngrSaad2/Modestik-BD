@extends('layouts.frontend')

@section('title')
    Search results for "{{ $q }}" - Modestik
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <h4 class="fw-bold mb-4">Search Results for: <span class="text-primary">"{{ $q }}"</span></h4>

        <div class="row g-3">
            @forelse($products as $product)
                @include('components.product-card', ['product' => $product])
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search-minus text-muted mb-3" style="font-size: 48px;"></i>
                    <h5 class="text-muted">No products found for "{{ $q }}"</h5>
                    <p class="text-muted">Please check spelling or use simpler keywords</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Browse all products</a>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection
