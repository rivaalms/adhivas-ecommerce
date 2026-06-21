<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use OpenApi\Attributes as OA;

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
#[OA\Schema(
    schema: 'Product',
    type: 'object',
    required: ['id', 'name', 'description', 'price', 'stock_quantity'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
        new OA\Property(property: 'description', type: 'string', example: 'High-end smartphone'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
        new OA\Property(property: 'stock_quantity', type: 'integer', example: 50),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'http://localhost:8000/storage/products/image.jpg'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
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

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
