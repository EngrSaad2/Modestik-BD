<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'brand_id', 'tax_id', 'name', 'slug', 'sku', 'barcode',
        'short_description', 'description', 'price', 'sale_price', 'cost',
        'quantity', 'min_order', 'max_order', 'weight', 'is_featured',
        'is_trending', 'is_flash_sale', 'flash_sale_price', 'flash_sale_start',
        'flash_sale_end', 'has_variants', 'meta_title', 'meta_description',
        'meta_keywords', 'og_image', 'structured_data', 'status', 'views',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_start' => 'datetime',
        'flash_sale_end' => 'datetime',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_flash_sale' => 'boolean',
        'has_variants' => 'boolean',
        'structured_data' => 'array',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')
            ->wherePivot('type', 'related');
    }

    public function crossSellProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')
            ->wherePivot('type', 'cross_sell');
    }

    public function upsellProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')
            ->wherePivot('type', 'upsell');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_products');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)
            ->where('flash_sale_start', '<=', now())
            ->where('flash_sale_end', '>=', now());
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        if ($this->is_flash_sale && $this->flash_sale_price && $this->flash_sale_start <= now() && $this->flash_sale_end >= now()) {
            return $this->flash_sale_price;
        }
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->has_variants) {
            return 0;
        }
        $currentPrice = $this->current_price;
        if ($currentPrice < $this->price) {
            return round((($this->price - $currentPrice) / $this->price) * 100);
        }
        return 0;
    }

    public function getProfitAttribute()
    {
        return max(0, $this->current_price - ($this->cost ?? 0));
    }

    public function getProfitMarginAttribute()
    {
        if ($this->current_price > 0) {
            return round(($this->profit / $this->current_price) * 100, 1);
        }
        return 0;
    }

    public function getPrimaryImageUrlAttribute()
    {
        $image = $this->primaryImage;
        $path = null;

        if ($image) {
            $path = $image->image;
        } else {
            $firstImage = $this->images->first();
            $path = $firstImage ? $firstImage->image : null;
        }

        if ($path && file_exists(storage_path('app/public/' . $path))) {
            // Serve WebP version if it exists (much smaller file size)
            $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $path);
            if ($webpPath !== $path && file_exists(storage_path('app/public/' . $webpPath))) {
                return asset('storage/' . $webpPath);
            }
            return asset('storage/' . $path);
        }

        // Premium fallbacks for Modestik products to avoid broken images
        $name = strtolower($this->name);
        if (str_contains($name, 'nail')) {
            return 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=500&auto=format&fit=crop&q=80'; // Nail focus
        }
        if (str_contains($name, 'stencil') || str_contains($name, 'sticker')) {
            return 'https://images.unsplash.com/photo-1590156221122-c848522676f8?w=500&auto=format&fit=crop&q=80'; // Stencils / Henna design
        }
        if (str_contains($name, 'oil')) {
            return 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=500&auto=format&fit=crop&q=80'; // Essential oils
        }
        // Fallback for organic cones & general mehendi
        return 'https://images.unsplash.com/photo-1590156546746-758b29f9cf0f?w=500&auto=format&fit=crop&q=80';
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function getIsInStockAttribute()
    {
        return $this->quantity > 0;
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->has_variants) {
            $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
            if ($variants->count() > 0) {
                $prices = $variants->map(function ($v) {
                    return (float) ($v->sale_price ?? $v->price ?? $this->current_price);
                })->filter(fn($p) => $p > 0);

                if ($prices->count() > 0) {
                    $minPrice = $prices->min();
                    $maxPrice = $prices->max();
                    if ($minPrice < $maxPrice) {
                        return '৳' . number_format($minPrice, 2) . ' - ৳' . number_format($maxPrice, 2);
                    } else {
                        return '৳' . number_format($minPrice, 2);
                    }
                }
            }
        }
        return '৳' . number_format($this->current_price, 2);
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return '৳' . number_format($this->price, 2);
    }
}
