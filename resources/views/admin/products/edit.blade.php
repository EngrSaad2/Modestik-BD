@extends('layouts.admin')

@section('title', 'Edit Product')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Edit Product</li>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        overflow: hidden;
    }
    .note-editor.note-frame .note-toolbar {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .note-editor.note-frame .note-editing-area .note-editable {
        background-color: #fff;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.6;
        color: #334155;
        min-height: 250px;
    }
    .note-btn {
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
        color: #475569 !important;
        border-radius: 4px !important;
        padding: 4px 8px !important;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" id="productDescription" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Side configuration panel -->
            <div class="col-md-4">
                <div class="bg-light p-3 rounded mb-3 border">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select select2" required>
                            <option value="" disabled>Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select select2">
                            <option value="">None</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>

                <div class="bg-light p-3 rounded mb-3 border">
                    <div class="mb-3">
                        <label class="form-label">Product Cost (৳)</label>
                        <input type="number" name="cost" class="form-control" value="{{ old('cost', $product->cost) }}" placeholder="Production / Purchase cost" step="0.01">
                        <small class="text-muted" style="font-size:11px;">Used to calculate actual profit margin.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Regular Price (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price (৳)</label>
                        <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Stock Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" required>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            <div class="col-12 border-top pt-4">
                <h5 class="fw-bold mb-3">Current Images</h5>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @forelse($product->images as $image)
                        <div class="position-relative" id="image-{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Product Image"
                                style="width:100px; height:100px; object-fit:cover; border-radius:8px; border:2px solid {{ $image->is_primary ? 'var(--admin-primary)' : '#e2e8f0' }};">
                            @if($image->is_primary)
                                <span class="badge bg-success position-absolute" style="top:-6px;left:-6px;font-size:9px;">Primary</span>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger position-absolute delete-image-btn"
                                style="top:-6px;right:-6px;padding:1px 5px;font-size:10px;border-radius:50%;"
                                data-image-id="{{ $image->id }}"
                                data-url="{{ route('admin.products.images.delete', $image->id) }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No images uploaded.</p>
                    @endforelse
                </div>

                <h5 class="fw-bold mb-3">Upload New Images</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Replace Primary Image</label>
                        <input type="file" name="primary_image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Add Gallery Images</label>
                        <input type="file" name="gallery_images[]" class="form-control" multiple>
                    </div>
                </div>
            </div>

            <!-- SEO Panel -->
            <div class="col-12 border-top pt-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-search me-2 text-muted"></i>Product Search Engine Optimization (SEO)</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">SEO Friendly URL Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" class="form-control" placeholder="Comma separated keywords...">{{ old('meta_keywords', $product->meta_keywords) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Variants Section -->
            <div class="col-12 border-top pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-layer-group me-2 text-muted"></i>Product Variants</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_variants" value="1" id="hasVariantsToggle"
                            {{ old('has_variants', $product->has_variants) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="hasVariantsToggle">Enable Variants</label>
                    </div>
                </div>

                <div id="variantsSection" style="display: none;">
                    <div class="alert alert-info py-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Add variants for different sizes, colors, etc. Each variant can have its own price, stock & SKU.
                    </div>

                    {{-- Existing Variants --}}
                    @if($product->variants->count() > 0)
                        <div class="mb-3">
                            <h6 class="fw-semibold text-muted mb-2">Existing Variants</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size:13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Image</th>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Cost</th>
                                            <th>Price</th>
                                            <th>Sale Price</th>
                                            <th>Qty</th>
                                            <th>Status</th>
                                            <th style="width:60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr id="existing-variant-{{ $variant->id }}">
                                                <td>
                                                    @if($variant->image_url)
                                                        <img src="{{ $variant->image_url }}" alt="variant" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                                    @else
                                                        <span class="text-muted" style="font-size:10px;">No image</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach($variant->values as $vv)
                                                        <span class="badge bg-secondary me-1">{{ $vv->attribute->name }}: {{ $vv->attributeValue->value }}</span>
                                                    @endforeach
                                                </td>
                                                <td><code>{{ $variant->sku }}</code></td>
                                                <td class="text-danger fw-bold">৳{{ number_format($variant->cost ?? $product->cost ?? 0, 2) }}</td>
                                                <td>৳{{ number_format($variant->price, 2) }}</td>
                                                <td>{{ $variant->sale_price ? '৳' . number_format($variant->sale_price, 2) : '-' }}</td>
                                                <td>{{ $variant->quantity }}</td>
                                                <td>
                                                    <span class="badge {{ $variant->status ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $variant->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-variant-btn"
                                                        data-variant-id="{{ $variant->id }}"
                                                        data-url="{{ route('admin.products.variants.delete', $variant->id) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <h6 class="fw-semibold text-muted mb-2">Add New Variants</h6>
                    <div id="variantsList">
                        {{-- New variant rows injected here by JS --}}
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addVariantBtn">
                        <i class="fas fa-plus me-1"></i>Add Variant
                    </button>
                </div>
            </div>

            <!-- Custom flags panel -->
            <div class="col-12 border-top pt-4">
                <h5 class="fw-bold mb-3">Product Tags & Status</h5>
                <div class="d-flex gap-4 flex-wrap">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="is_trending"
                            {{ old('is_trending', $product->is_trending) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_trending">Trending Product</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_flash_sale" value="1" id="is_flash_sale"
                            {{ old('is_flash_sale', $product->is_flash_sale) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_flash_sale">Flash Sale Product</label>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn btn-admin-primary px-5 py-2 fw-bold">Update Product</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
$(function() {
    // Summernote editor for product description
    $('#productDescription').summernote({
        placeholder: 'Enter full product description, features, instructions...',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    $('#productForm').on('submit', function() {
        if ($('#productDescription').summernote('isEmpty')) {
            $('#productDescription').val('');
        } else {
            $('#productDescription').val($('#productDescription').summernote('code'));
        }
    });

    const attributes = @json($attributes);
    let variantIndex = 0;

    // Toggle variants section
    function toggleVariants() {
        const checked = $('#hasVariantsToggle').is(':checked');
        $('#variantsSection').toggle(checked);
    }
    $('#hasVariantsToggle').on('change', toggleVariants);
    toggleVariants();

    // Build attribute select HTML
    function buildAttributeSelects(idx) {
        let html = '';
        attributes.forEach(function(attr) {
            html += `<div class="col-md-3 mb-2">
                <label class="form-label text-muted" style="font-size:12px;">${attr.name}</label>
                <select name="variants[${idx}][values][${attr.id}]" class="form-select form-select-sm">
                    <option value="">-- Select --</option>`;
            attr.values.forEach(function(val) {
                html += `<option value="${val.id}">${val.value}</option>`;
            });
            html += `</select></div>`;
        });
        return html;
    }

    // Add variant row
    function addVariantRow() {
        const idx = variantIndex++;
        const row = `
        <div class="variant-row border rounded p-3 mb-3 bg-light position-relative" data-variant-idx="${idx}">
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute remove-variant-btn" style="top:8px;right:8px;" title="Remove variant">
                <i class="fas fa-times"></i>
            </button>
            <div class="row g-2 mb-2">
                <div class="col-12"><strong class="text-muted" style="font-size:12px;">NEW VARIANT #${idx + 1} — Attributes</strong></div>
                ${buildAttributeSelects(idx)}
                <div class="col-md-3 mb-2">
                    <label class="form-label text-muted" style="font-size:12px;">Variant Image</label>
                    <input type="file" name="variant_images[${idx}]" class="form-control form-control-sm" accept="image/*">
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label text-muted" style="font-size:12px;">Cost Price (৳)</label>
                    <input type="number" name="variants[${idx}][cost]" class="form-control form-control-sm" placeholder="Cost" step="0.01">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted" style="font-size:12px;">Price (৳)</label>
                    <input type="number" name="variants[${idx}][price]" class="form-control form-control-sm" placeholder="Variant price" step="0.01">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted" style="font-size:12px;">Sale Price (৳)</label>
                    <input type="number" name="variants[${idx}][sale_price]" class="form-control form-control-sm" placeholder="Sale price" step="0.01">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted" style="font-size:12px;">Quantity</label>
                    <input type="number" name="variants[${idx}][quantity]" class="form-control form-control-sm" value="0" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted" style="font-size:12px;">SKU</label>
                    <input type="text" name="variants[${idx}][sku]" class="form-control form-control-sm" placeholder="Auto if blank">
                </div>
            </div>
        </div>`;
        $('#variantsList').append(row);
    }

    $('#addVariantBtn').on('click', addVariantRow);

    // Remove new variant row
    $(document).on('click', '.remove-variant-btn', function() {
        $(this).closest('.variant-row').fadeOut(200, function() { $(this).remove(); });
    });

    // Delete existing variant via AJAX
    $(document).on('click', '.delete-variant-btn', function() {
        const btn = $(this);
        const variantId = btn.data('variant-id');
        const url = btn.data('url') || `{{ url('admin/products/variants') }}/${variantId}`;
        const row = btn.closest('tr');
        Swal.fire({
            title: 'Delete this variant?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        row.fadeOut(200, function() { $(this).remove(); });
                        Swal.fire({ icon: 'success', title: 'Variant deleted', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to delete variant.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });

    // Delete product image via AJAX
    $(document).on('click', '.delete-image-btn', function() {
        const btn = $(this);
        const imageId = btn.data('image-id');
        const url = btn.data('url') || `{{ url('admin/products/images') }}/${imageId}`;
        const wrapper = btn.closest('.position-relative');
        Swal.fire({
            title: 'Delete this image?',
            text: 'This image will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        wrapper.fadeOut(200, function() { $(this).remove(); });
                        Swal.fire({ icon: 'success', title: 'Image deleted', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to delete image.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
