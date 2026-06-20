<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Database\Factories\ProductFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $stock_quantity
 * @property string|null $image_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'description',
    'price',
    'stock_quantity',
    'image_url',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'stock_quantity' => 'integer',
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
}
