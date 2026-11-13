<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Coupon extends Model
{
    public const APPLIES_FULL_STORE = 'full_store';
    public const APPLIES_SPECIFIC_PRODUCTS = 'specific_products';
    public const APPLIES_SPECIFIC_CATEGORIES = 'specific_categories';

    protected $fillable = [
        'code',
        'type',
        'value',
        'applies_to',
        'product_ids',
        'category_ids',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'free_shipping',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'product_ids' => 'array',
            'category_ids' => 'array',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'free_shipping' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFullStore(): bool
    {
        return empty($this->applies_to) || $this->applies_to === self::APPLIES_FULL_STORE;
    }

    public function isSpecificProducts(): bool
    {
        return $this->applies_to === self::APPLIES_SPECIFIC_PRODUCTS;
    }

    public function isSpecificCategories(): bool
    {
        return $this->applies_to === self::APPLIES_SPECIFIC_CATEGORIES;
    }

    /** Has this coupon run out of uses, expired, or not started yet? */
    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the subtotal of items eligible for this coupon.
     */
    public function eligibleItemsSubtotal(?Collection $cartItems, float $cartSubtotal = 0.0): float
    {
        if ($this->isFullStore() || ! $cartItems || $cartItems->isEmpty()) {
            return $cartSubtotal;
        }

        if ($this->isSpecificProducts()) {
            $productIds = collect($this->product_ids ?? [])->map(fn ($id) => (int) $id)->all();
            if (empty($productIds)) {
                return 0.0;
            }

            return (float) $cartItems
                ->filter(fn ($item) => in_array((int) ($item->product->id ?? $item->product_id ?? 0), $productIds, true))
                ->sum('subtotal');
        }

        if ($this->isSpecificCategories()) {
            $categoryIds = collect($this->category_ids ?? [])->map(fn ($id) => (int) $id)->all();
            if (empty($categoryIds)) {
                return 0.0;
            }

            return (float) $cartItems
                ->filter(function ($item) use ($categoryIds) {
                    $catId = (int) ($item->product->category_id ?? 0);
                    $parentCatId = (int) ($item->product->category?->parent_id ?? 0);

                    return in_array($catId, $categoryIds, true) || ($parentCatId && in_array($parentCatId, $categoryIds, true));
                })
                ->sum('subtotal');
        }

        return $cartSubtotal;
    }

    /**
     * Check if the cart has items eligible for this coupon.
     */
    public function hasEligibleItems(?Collection $cartItems): bool
    {
        if ($this->isFullStore()) {
            return true;
        }

        if (! $cartItems || $cartItems->isEmpty()) {
            return false;
        }

        return $this->eligibleItemsSubtotal($cartItems) > 0;
    }

    /** Why the coupon can't be used right now, or null when it's fine. */
    public function reasonUnusableFor(float $subtotal, ?Collection $cartItems = null): ?string
    {
        if (! $this->isUsable()) {
            return 'This coupon is no longer valid.';
        }

        if ($cartItems !== null && ! $cartItems->isEmpty()) {
            if ($this->isSpecificProducts()) {
                $configuredIds = $this->product_ids ?? [];
                if (empty($configuredIds)) {
                    return 'No products configured for this coupon.';
                }
                if (! $this->hasEligibleItems($cartItems)) {
                    return 'This coupon is only valid for selected products.';
                }
            } elseif ($this->isSpecificCategories()) {
                $configuredCatIds = $this->category_ids ?? [];
                if (empty($configuredCatIds)) {
                    return 'No categories configured for this coupon.';
                }
                if (! $this->hasEligibleItems($cartItems)) {
                    return 'This coupon is only valid for selected categories.';
                }
            }
        }

        if ($this->min_order_amount && $subtotal < (float) $this->min_order_amount) {
            return 'Minimum order of ৳'.number_format((float) $this->min_order_amount).' is required for this coupon.';
        }

        return null;
    }

    /** Money off the eligible subtotal (never more than the eligible subtotal itself). */
    public function discountFor(float $subtotal, ?Collection $cartItems = null): float
    {
        $base = $this->eligibleItemsSubtotal($cartItems, $subtotal);

        if ($base <= 0) {
            return 0.0;
        }

        $discount = $this->type === 'percent'
            ? $base * ((float) $this->value / 100)
            : (float) $this->value;

        if ($this->type === 'percent' && $this->max_discount) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round(min($discount, $base), 2);
    }

    public function getLabelAttribute(): string
    {
        return $this->type === 'percent'
            ? rtrim(rtrim((string) $this->value, '0'), '.').'% off'
            : '৳'.number_format((float) $this->value).' off';
    }
}
