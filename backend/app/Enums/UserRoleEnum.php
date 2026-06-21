<?php

namespace App\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string')]
enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
}
