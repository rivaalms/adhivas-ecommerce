<?php

namespace App\Enums\Enum;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string')]
enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
