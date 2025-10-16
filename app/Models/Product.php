<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category',
        'category_id',
        'description',
        'image_url',
        'stock',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];

    protected $appends = [
        'category_name',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price / 100, 2);
    }

    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->categoryModel?->name ?? $this->category;
    }
}
