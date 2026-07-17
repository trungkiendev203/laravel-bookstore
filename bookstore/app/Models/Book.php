<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'parent_id',
        'title',
        'author',
        'slug',
        'volume_number',
        'price',
        'sale_price',
        'stock',
        'description',
        'cover_image',
        'is_active',
        'sold_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
        'sold_count' => 'integer',
    ];

    /**
     * Một truyện thuộc về một thể loại
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Một tập truyện thuộc về một truyện cha (series)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'parent_id');
    }

    /**
     * Một bộ truyện (cha) có nhiều tập truyện (con)
     */
    public function volumes(): HasMany
    {
        return $this->hasMany(Book::class, 'parent_id')->orderByRaw('CAST(volume_number AS UNSIGNED) ASC')->orderBy('volume_number', 'asc');
    }

    /**
     * Một truyện có trong nhiều chi tiết đơn hàng
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Một truyện có nhiều bình luận/đánh giá
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Helper check xem còn hàng không
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Nếu là truyện cha và có tập con
        if (is_null($this->parent_id) && $this->volumes()->exists()) {
            return $this->volumes()->where('is_active', true)->where('stock', '>', 0)->exists();
        }

        return $this->stock > 0;
    }

    /**
     * Lấy giá hiện tại (nếu có giá sale_price thì dùng sale_price)
     */
    public function getActivePriceAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price 
            ? $this->sale_price 
            : $this->price;
    }

    /**
     * Lấy URL đầy đủ của ảnh bìa (hỗ trợ cả ảnh tải lên và link ngoài)
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return asset('storage/' . $this->cover_image);
    }
}
