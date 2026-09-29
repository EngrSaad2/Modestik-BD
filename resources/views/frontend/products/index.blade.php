@extends('layouts.frontend')

@section('title')
    {{ isset($category) ? ($category->meta_title ?? ($category->name . ' - Modestik')) : (isset($brand) ? $brand->name . ' - Modestik' : 'Shop - Modestik') }}
@endsection
@section('meta_description')
    {{ isset($category) ? ($category->meta_description ?? ($category->description ?? 'Browse the best ' . $category->name . ' products at Modestik.')) : 'Shop the latest deals and products online at Modestik.' }}
@endsection
@section('meta_keywords')
    {{ isset($category) ? ($category->meta_keywords ?? ($category->name . ', organic mehendi, halal beauty, Modestik')) : 'Modestik, organic mehendi, halal beauty' }}
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ isset($category) ? $category->name : (isset($brand) ? $brand->name : 'Shop') }}
                </li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Filters -->
            @if(!isset($category))
            <div class="col-lg-3">
                <div class="filter-sidebar">
                    <form action="{{ url()->current() }}" method="GET" id="filterForm">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <!-- Categories -->
                        <div class="filter-group">
                            <h6>Categories</h6>
                            @foreach($categories as $cat)
                                <div class="form-check mb-2">
                                    <input class="form-check-input filter-checkbox" type="checkbox" name="category" 
                                           value="{{ $cat->id }}" id="cat-{{ $cat->id }}" 
                                           {{ request('category') == $cat->id || (isset($category) && $category->id == $cat->id) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex justify-content-between" for="cat-{{ $cat->id }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-muted">({{ $cat->products_count }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Range -->
                        <!--
                        <div class="filter-group">
                            <h6>Price Range</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">Apply Price</button>
                        </div>
                        -->
                    </form>
                </div>
            </div>
            @endif

            <!-- Products Column -->
            <div class="{{ isset($category) ? 'col-lg-12' : 'col-lg-9' }}">
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm">
                    <div class="text-muted">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-nowrap text-muted" style="font-size:14px;">Sort By:</span>
                        <select class="form-select form-select-sm" id="sortSelect" style="width: 160px;">
                            <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popularity</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    @forelse($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-box-open text-muted mb-3" style="font-size:48px;"></i>
                            <h5 class="text-muted">No products found</h5>
                            <p class="text-muted">Try adjusting your filters or search terms</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Handle Sorting
    $('#sortSelect').change(function() {
        let sortVal = $(this).val();
        let url = new URL(window.location.href);
        url.searchParams.set('sort', sortVal);
        window.location.href = url.href;
    });

    // Handle Filter Checkboxes
    $('.filter-checkbox').change(function() {
        $('#filterForm').submit();
    });
</script>
@endsection
