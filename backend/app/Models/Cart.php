<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

/**
 * @property int $id
 * @property int $user_id
 */
#[OA\Schema(
    schema: 'Cart',
    type: 'object',
    required: ['id', 'user_id'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
    ]
)]
#[Fillable(['user_id'])]
class Cart extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
