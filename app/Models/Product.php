<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_usd',
        'stock',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price_usd' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class)
            ->orderBy('sort_order');
    }

    public function orders()
    {
        return $this->belongsToMany(
            Order::class,
            'order_items'
        )->withPivot([
            'product_name',
            'unit_price_usd',
            'quantity',
            'total_usd',
        ]);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(
            User::class,
            'favorites'
        )->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available'
            && $this->stock > 0;
    }
}
