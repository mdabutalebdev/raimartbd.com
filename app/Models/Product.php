<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'main_image',
        'short_description',
        'description',
        'price',
        'old_price',
        'stock',
        'sku',
        'weight',
        'rating',
        'reviews_count',
        'is_featured',
        'is_best_seller',
        'is_new_arrival',
        'is_top_selling',
        'top_selling_order',
        'is_active',
        'attributes',
        'meta_title',
        'meta_description',
        'sales_metrics_mode',
        'base_sold_count',
        'sales_start_date',
        'daily_sales_volume',
        'rating_metrics_mode',
        'base_reviews_count',
        'rating_start_date',
        'rating_interval_days',
        'ratings_per_interval',
        'min_rating',
        'max_rating',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_top_selling' => 'boolean',
            'is_active' => 'boolean',
            'attributes' => 'array',
            'sales_start_date' => 'date',
            'rating_start_date' => 'date',
            'min_rating' => 'decimal:2',
            'max_rating' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function hasVariants(): bool
    {
        return $this->relationLoaded('variants')
            ? $this->variants->isNotEmpty()
            : $this->variants()->exists();
    }

    public function refreshReviewStats(): void
    {
        $approved = $this->reviews()->approved();
        $this->update([
            'rating' => round((float) $approved->avg('rating'), 1),
            'reviews_count' => (int) $approved->count(),
        ]);
    }

    public function getMainImageAttribute($value)
    {
        return $value ?? $this->images->first()?->image;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->old_price || $this->old_price <= $this->price) {
            return null;
        }

        return (int) round((($this->old_price - $this->price) / $this->old_price) * 100);
    }

    /**
     * Calculate display sold units (Realistic, deterministic, refresh-safe auto-growth)
     */
    public function getDisplaySoldCountAttribute(): int
    {
        $actualSales = $this->relationLoaded('orderItems')
            ? (int) $this->orderItems->sum('quantity')
            : (int) $this->orderItems()->sum('quantity');

        if ($this->sales_metrics_mode !== 'optimized') {
            return max(1, $actualSales ?: ($this->order_items_count ?? 1));
        }

        $base = (int) ($this->base_sold_count ?? 0);
        $startDate = $this->sales_start_date ? \Carbon\Carbon::parse($this->sales_start_date) : ($this->created_at ?? now());
        $today = \Carbon\Carbon::today();

        $simulatedSales = 0;
        if ($today->greaterThanOrEqualTo($startDate)) {
            $daysElapsed = (int) $startDate->diffInDays($today);
            $dailyVolume = max(0, (int) ($this->daily_sales_volume ?? 2));

            if ($dailyVolume > 0 && $daysElapsed > 0) {
                $individualDays = min($daysElapsed, 90);
                $earlierDays = $daysElapsed - $individualDays;
                $simulatedSales += $earlierDays * $dailyVolume;

                for ($d = $daysElapsed - $individualDays; $d <= $daysElapsed; $d++) {
                    $dayDateStr = $startDate->copy()->addDays($d)->format('Y-m-d');
                    $seed = abs(crc32($this->id . '-sales-' . $dayDateStr));
                    $mod = $seed % 10;
                    if ($dailyVolume === 1) {
                        $daySales = ($mod < 4) ? 0 : (($mod < 8) ? 1 : 2);
                    } elseif ($dailyVolume === 2) {
                        $daySales = ($mod < 2) ? 0 : (($mod < 5) ? 1 : (($mod < 8) ? 2 : (($mod === 8) ? 3 : 4)));
                    } else {
                        $variance = ($mod % 3) - 1;
                        $daySales = max(0, $dailyVolume + $variance);
                    }
                    $simulatedSales += $daySales;
                }
            }
        }

        $total = $base + $simulatedSales + $actualSales;
        return max(1, $total);
    }

    /**
     * Calculate display rating score (Deterministic and authentic score)
     */
    public function getDisplayRatingAttribute(): float
    {
        if ($this->rating_metrics_mode !== 'optimized') {
            return (float) ($this->rating ?? 0.0);
        }

        $min = (float) ($this->min_rating ?? 4.50);
        $max = (float) ($this->max_rating ?? 5.00);
        if ($min > $max) {
            $temp = $min; $min = $max; $max = $temp;
        }

        $seed = abs(crc32($this->id . '-rating-score'));
        $fraction = ($seed % 100) / 100.0;
        $simulated = round($min + (($max - $min) * $fraction), 1);

        return min(5.0, max(1.0, $simulated));
    }

    /**
     * Calculate display reviews count (Deterministic interval-based auto-growth)
     */
    public function getDisplayReviewsCountAttribute(): int
    {
        $actualReviews = (int) ($this->reviews_count ?? 0);

        if ($this->rating_metrics_mode !== 'optimized') {
            return $actualReviews;
        }

        $base = (int) ($this->base_reviews_count ?? 0);
        $startDate = $this->rating_start_date ? \Carbon\Carbon::parse($this->rating_start_date) : ($this->created_at ?? now());
        $today = \Carbon\Carbon::today();

        $simulatedReviews = 0;
        if ($today->greaterThanOrEqualTo($startDate)) {
            $daysElapsed = (int) $startDate->diffInDays($today);
            $intervalDays = max(1, (int) ($this->rating_interval_days ?? 4));
            $ratingsPerInterval = max(1, (int) ($this->ratings_per_interval ?? 1));

            $intervalsCount = (int) floor($daysElapsed / $intervalDays);
            if ($intervalsCount > 0) {
                $individualIntervals = min($intervalsCount, 60);
                $earlierIntervals = $intervalsCount - $individualIntervals;
                $simulatedReviews += $earlierIntervals * $ratingsPerInterval;

                for ($i = $intervalsCount - $individualIntervals; $i <= $intervalsCount; $i++) {
                    $seed = abs(crc32($this->id . '-review-interval-' . $i));
                    $mod = $seed % 10;
                    $intervalAdd = ($mod < 2) ? 0 : (($mod < 8) ? $ratingsPerInterval : ($ratingsPerInterval + 1));
                    $simulatedReviews += $intervalAdd;
                }
            }
        }

        return $base + $simulatedReviews + $actualReviews;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeNewArrival($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeTopSelling($query)
    {
        return $query->where('is_top_selling', true);
    }

    protected static function booted(): void
    {
        static::saved(fn () => \App\Http\Controllers\SitemapController::clearCache());
        static::deleted(fn () => \App\Http\Controllers\SitemapController::clearCache());
    }
}
