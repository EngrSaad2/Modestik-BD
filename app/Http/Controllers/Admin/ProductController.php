<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Brand, Tax, ProductImage, ProductVariant, ProductVariantValue, Attribute};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images', 'primaryImage']);

        if ($request->has('trash') && $request->trash == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        // Calculate Stats
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'total_stock' => Product::sum('quantity'),
            'total_cost' => Product::selectRaw('SUM(cost * quantity) as total')->value('total') ?? 0,
            'total_value' => Product::selectRaw('SUM(price * quantity) as total')->value('total') ?? 0,
        ];
        $stats['potential_profit'] = max(0, $stats['total_value'] - $stats['total_cost']);

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $taxes = Tax::active()->get();
        $attributes = Attribute::active()->with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'taxes', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'primary_image' => 'required|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create(array_merge($request->all(), [
                'slug' => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
                'sku' => $request->sku ?? 'SKU-' . strtoupper(Str::random(8)),
                'cost' => $request->cost ?? 0,
                'meta_keywords' => $request->meta_keywords ?? null,
                'is_featured' => $request->has('is_featured'),
                'is_trending' => $request->has('is_trending'),
                'is_flash_sale' => $request->has('is_flash_sale'),
                'has_variants' => $request->has('has_variants'),
            ]));

            // Upload Primary Image
            if ($request->hasFile('primary_image')) {
                $path = $request->file('primary_image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => true,
                ]);
            }

            // Upload Gallery Images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $img) {
                    $path = $img->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'is_primary' => false,
                    ]);
                }
            }

            // Variants generation
            if ($request->has('has_variants') && $request->has('variants')) {
                foreach ($request->variants as $idx => $variantData) {
                    $values = array_filter($variantData['values'] ?? [], fn($v) => !empty($v));
                    if (empty($values)) continue;

                    $variantImagePath = null;
                    if ($request->hasFile("variant_images.{$idx}")) {
                        $variantImagePath = $request->file("variant_images.{$idx}")->store('products/variants', 'public');
                    }

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? $product->sku . '-' . rand(100, 999),
                        'image' => $variantImagePath,
                        'price' => $variantData['price'] ?? $product->price,
                        'cost' => $variantData['cost'] ?? $product->cost,
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'quantity' => $variantData['quantity'] ?? 0,
                    ]);

                    foreach ($values as $attrId => $valId) {
                        ProductVariantValue::create([
                            'variant_id' => $variant->id,
                            'attribute_id' => $attrId,
                            'attribute_value_id' => $valId,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Product store failed: ' . $e->getMessage()]);
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $taxes = Tax::active()->get();
        $attributes = Attribute::active()->with('values')->get();

        $product->load(['images', 'variants.values.attribute', 'variants.values.attributeValue']);

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'taxes', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $product->update(array_merge($request->all(), [
                'slug' => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
                'cost' => $request->cost ?? 0,
                'meta_keywords' => $request->meta_keywords ?? null,
                'is_featured' => $request->has('is_featured'),
                'is_trending' => $request->has('is_trending'),
                'is_flash_sale' => $request->has('is_flash_sale'),
                'has_variants' => $request->has('has_variants'),
            ]));

            // Primary Image replacement
            if ($request->hasFile('primary_image')) {
                ProductImage::where('product_id', $product->id)->where('is_primary', true)->delete();
                $path = $request->file('primary_image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => true,
                ]);
            }

            // Gallery Images upload
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $img) {
                    $path = $img->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'is_primary' => false,
                    ]);
                }
            }

            // Create new variants if submitted
            if ($request->has('has_variants') && $request->has('variants')) {
                foreach ($request->variants as $idx => $variantData) {
                    $values = array_filter($variantData['values'] ?? [], fn($v) => !empty($v));
                    if (empty($values)) continue;

                    $variantImagePath = null;
                    if ($request->hasFile("variant_images.{$idx}")) {
                        $variantImagePath = $request->file("variant_images.{$idx}")->store('products/variants', 'public');
                    }

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? $product->sku . '-' . rand(100, 999),
                        'image' => $variantImagePath,
                        'price' => $variantData['price'] ?? $product->price,
                        'cost' => $variantData['cost'] ?? $product->cost,
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'quantity' => $variantData['quantity'] ?? 0,
                    ]);

                    foreach ($values as $attrId => $valId) {
                        ProductVariantValue::create([
                            'variant_id' => $variant->id,
                            'attribute_id' => $attrId,
                            'attribute_value_id' => $valId,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Product update failed: ' . $e->getMessage()]);
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'delete':
                Product::whereIn('id', $ids)->delete();
                $msg = 'Selected products soft-deleted successfully.';
                break;
            case 'publish':
                Product::whereIn('id', $ids)->update(['status' => 'active']);
                $msg = 'Selected products published successfully.';
                break;
            case 'unpublish':
                Product::whereIn('id', $ids)->update(['status' => 'inactive']);
                $msg = 'Selected products unpublished successfully.';
                break;
            case 'feature':
                Product::whereIn('id', $ids)->update(['is_featured' => true]);
                $msg = 'Selected products marked as featured.';
                break;
            case 'unfeature':
                Product::whereIn('id', $ids)->update(['is_featured' => false]);
                $msg = 'Selected products un-featured.';
                break;
            case 'restore':
                Product::onlyTrashed()->whereIn('id', $ids)->restore();
                $msg = 'Selected products restored successfully.';
                break;
            case 'force_delete':
                $products = Product::onlyTrashed()->whereIn('id', $ids)->get();
                foreach ($products as $p) {
                    foreach ($p->images as $img) {
                        if ($img->image && Storage::disk('public')->exists($img->image)) {
                            Storage::disk('public')->delete($img->image);
                        }
                    }
                    $p->forceDelete();
                }
                $msg = 'Selected products permanently deleted.';
                break;
            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return redirect()->route('admin.products.index')->with('success', $msg);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function clone(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->load(['images', 'variants.values', 'tags']);

            // Determine cloned product name
            $baseName = preg_replace('/\s+\(Copy(\s+\d+)?\)$/i', '', $product->name);
            $copyNumber = 1;
            $clonedName = $baseName . ' (Copy)';
            while (Product::where('name', $clonedName)->exists()) {
                $copyNumber++;
                $clonedName = $baseName . " (Copy {$copyNumber})";
            }

            // Generate unique slug
            $baseSlug = Str::slug($clonedName);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Generate unique SKU
            $baseSku = $product->sku ? preg_replace('/-COPY(-\d+)?$/i', '', $product->sku) . '-COPY' : 'SKU-' . strtoupper(Str::random(8));
            $sku = $baseSku;
            $sCounter = 1;
            while (Product::withTrashed()->where('sku', $sku)->exists()) {
                $sku = $baseSku . '-' . $sCounter;
                $sCounter++;
            }

            // Replicate product
            $newProduct = $product->replicate([
                'slug', 'sku', 'views', 'created_at', 'updated_at', 'deleted_at'
            ]);
            $newProduct->name = $clonedName;
            $newProduct->slug = $slug;
            $newProduct->sku = $sku;
            $newProduct->save();

            // Duplicate images
            foreach ($product->images as $img) {
                $newImagePath = null;
                if ($img->image && Storage::disk('public')->exists($img->image)) {
                    $ext = pathinfo($img->image, PATHINFO_EXTENSION);
                    $newFilename = 'products/' . Str::random(40) . ($ext ? '.' . $ext : '');
                    Storage::disk('public')->copy($img->image, $newFilename);
                    $newImagePath = $newFilename;
                } else {
                    $newImagePath = $img->image;
                }

                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image' => $newImagePath,
                    'alt' => $img->alt,
                    'is_primary' => $img->is_primary,
                    'order' => $img->order ?? 0,
                ]);
            }

            // Duplicate variants
            if ($product->has_variants && $product->variants->count() > 0) {
                foreach ($product->variants as $variant) {
                    $newVariantImagePath = null;
                    if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                        $ext = pathinfo($variant->image, PATHINFO_EXTENSION);
                        $newVariantImage = 'products/variants/' . Str::random(40) . ($ext ? '.' . $ext : '');
                        Storage::disk('public')->copy($variant->image, $newVariantImage);
                        $newVariantImagePath = $newVariantImage;
                    } else {
                        $newVariantImagePath = $variant->image;
                    }

                    $baseVarSku = $variant->sku ? preg_replace('/-COPY(-\d+)?$/i', '', $variant->sku) . '-COPY' : 'VAR-' . strtoupper(Str::random(6));
                    $varSku = $baseVarSku;
                    $vCounter = 1;
                    while (ProductVariant::where('sku', $varSku)->exists()) {
                        $varSku = $baseVarSku . '-' . $vCounter;
                        $vCounter++;
                    }

                    $newVariant = ProductVariant::create([
                        'product_id' => $newProduct->id,
                        'sku' => $varSku,
                        'image' => $newVariantImagePath,
                        'price' => $variant->price,
                        'cost' => $variant->cost,
                        'sale_price' => $variant->sale_price,
                        'quantity' => $variant->quantity,
                        'status' => $variant->status,
                    ]);

                    foreach ($variant->values as $val) {
                        ProductVariantValue::create([
                            'variant_id' => $newVariant->id,
                            'attribute_id' => $val->attribute_id,
                            'attribute_value_id' => $val->attribute_value_id,
                        ]);
                    }
                }
            }

            // Duplicate tags if any
            if ($product->relationLoaded('tags') && $product->tags->count() > 0) {
                $newProduct->tags()->sync($product->tags->pluck('id'));
            }

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', "Product cloned successfully as '{$clonedName}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to clone product: ' . $e->getMessage()]);
        }
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();

        // If primary image was deleted, promote another image to primary
        if ($wasPrimary) {
            $nextImage = ProductImage::where('product_id', $productId)->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    public function storeVariant(Request $request, Product $product)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'values' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $request->sku ?? $product->sku . '-' . rand(100, 999),
                'price' => $request->price ?? $product->price,
                'cost' => $request->cost ?? $product->cost,
                'sale_price' => $request->sale_price,
                'quantity' => $request->quantity ?? 0,
            ]);

            foreach ($request->values as $attrId => $valId) {
                if (!empty($valId)) {
                    ProductVariantValue::create([
                        'variant_id' => $variant->id,
                        'attribute_id' => $attrId,
                        'attribute_value_id' => $valId,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Variant added successfully.', 'variant' => $variant->load('values')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to add variant: ' . $e->getMessage()], 500);
        }
    }

    public function deleteVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return response()->json(['message' => 'Variant deleted successfully.']);
    }
}
