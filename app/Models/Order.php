<?php

namespace App\Models;

use App\Enums\Enum\OrderStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
