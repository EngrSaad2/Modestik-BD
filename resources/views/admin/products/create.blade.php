@extends('layouts.admin')

@section('title', 'Add Product')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Add Product</li>
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
    <h4 class="fw-bold mb-0">Add Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="form-card">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Wireless Bluetooth Earbuds" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" rows="2" class="form-control" placeholder="Short highlights of the product...">{{ old('short_description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Description</label>
                    <textarea name="description" id="productDescription" rows="6" class="form-control" placeholder="Full specifications list...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Side configuration panel -->
            <div class="col-md-4">
                <div class="bg-light p-3 rounded mb-3 border">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select select2" required>
                            <option value="" disabled selected>Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select select2">
                            <option value="">None</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product SKU (Auto if blank)</label>
                        <input type="text" name="sku" class="form-control" placeholder="e.g. EAR-PRO-01" value="{{ old('sku') }}">
                    </div>
                </div>

                <div class="bg-light p-3 rounded mb-3 border" id="basePricingPanel">
                    <div class="mb-3">
                        <label class="form-label">Product Cost (৳)</label>
                        <input type="number" name="cost" class="form-control" placeholder="Purchase / Production cost" value="{{ old('cost') }}" step="0.01">
                        <small class="text-muted" style="font-size:11px;">Used to calculate actual profit margin.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Regular Price (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="e.g. 1500" value="{{ old('price') }}" required step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price (৳)</label>
                        <input type="number" name="sale_price" class="form-control" placeholder="e.g. 1200" value="{{ old('sale_price') }}" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Stock Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 10) }}" required>
                    </div>
                </div>
            </div>

            <!-- Image upload panel -->
            <div class="col-12 border-top pt-4">
                <h5 class="fw-bold mb-3">Product Media</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Primary Image <span class="text-danger">*</span></label>
                        <input type="file" name="primary_image" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gallery Images</label>
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
                        <input type="text" name="slug" class="form-control" placeholder="Auto generated from product name" value="{{ old('slug') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" placeholder="Meta title for Google" value="{{ old('meta_title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control" placeholder="Meta description for search engines...">{{ old('meta_description') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" class="form-control" placeholder="Comma separated keywords (e.g. organic, mehendi, halal beauty)...">{{ old('meta_keywords') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Variants Section -->
            <div class="col-12 border-top pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-layer-group me-2 text-muted"></i>Product Variants</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_variants" value="1" id="hasVariantsToggle" {{ old('has_variants') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="hasVariantsToggle">Enable Variants</label>
                    </div>
                </div>

                <div id="variantsSection" style="display: none;">
                    <div class="alert alert-info py-2 mb-3" style="font-size: 13px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Add variants for different sizes, colors, etc. Each variant can have its own price, cost, stock & SKU.
                    </div>

                    <div id="variantsList">
                        {{-- Variant rows injected here by JS --}}
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
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="is_trending" {{ old('is_trending') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_trending">Trending Product</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_flash_sale" value="1" id="is_flash_sale" {{ old('is_flash_sale') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_flash_sale">Flash Sale Product</label>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn btn-admin-primary px-5 py-2 fw-bold">Save Product</button>
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
        placeholder: 'Write full product description, features, instructions...',
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
                <div class="col-12"><strong class="text-muted" style="font-size:12px;">VARIANT #${idx + 1} — Attributes</strong></div>
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

    // Remove variant row
    $(document).on('click', '.remove-variant-btn', function() {
        $(this).closest('.variant-row').fadeOut(200, function() { $(this).remove(); });
    });
});
</script>
@endsection
