<?php

namespace App\Models;

use App\Enums\Enum\OrderStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use OpenApi\Attributes as OA;

/**
 * @property int $id
 * @property int $user_id
 * @property int $user_address_id
 * @property Carbon $order_date
 * @property float $total_amount
 * @property OrderStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[OA\Schema(
    schema: 'Order',
    type: 'object',
    required: ['id', 'user_id', 'user_address_id', 'order_date', 'total_amount', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_address_id', type: 'integer', example: 1),
        new OA\Property(property: 'order_date', type: 'string', format: 'date-time'),
        new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 500.00),
        new OA\Property(property: 'status', ref: OrderStatusEnum::class),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[Fillable(['user_id', 'user_address_id', 'order_date', 'total_amount', 'status'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'total_amount' => 'float',
            'status' => OrderStatusEnum::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
