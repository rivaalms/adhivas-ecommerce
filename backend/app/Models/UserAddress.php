<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

/**
 * @property int $id
 * @property int $user_id
 * @property string $address_line
 * @property string $subdistrict_id
 * @property string $subdistrict_name
 * @property string $district_id
 * @property string $district_name
 * @property string $regency_id
 * @property string $regency_name
 * @property string $province_id
 * @property string $province_name
 * @property string $postal_code
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[OA\Schema(
    schema: 'UserAddress',
    type: 'object',
    required: ['id', 'user_id', 'subdistrict_id', 'subdistrict_name', 'district_id', 'district_name', 'regency_id', 'regency_name', 'province_id', 'province_name', 'postal_code'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'address_line', type: 'string', nullable: true, example: 'Jl. Merdeka No. 1'),
        new OA\Property(property: 'subdistrict_id', type: 'string', example: '11'),
        new OA\Property(property: 'subdistrict_name', type: 'string', example: 'Menteng'),
        new OA\Property(property: 'district_id', type: 'string', example: '1'),
        new OA\Property(property: 'district_name', type: 'string', example: 'Jakarta Pusat'),
        new OA\Property(property: 'regency_id', type: 'string', example: '31'),
        new OA\Property(property: 'regency_name', type: 'string', example: 'DKI Jakarta'),
        new OA\Property(property: 'province_id', type: 'string', example: '31'),
        new OA\Property(property: 'province_name', type: 'string', example: 'DKI Jakarta'),
        new OA\Property(property: 'postal_code', type: 'string', example: '10310'),
        new OA\Property(property: 'is_default', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[Fillable(['user_id', 'address_line', 'subdistrict_id', 'subdistrict_name', 'district_id', 'district_name', 'regency_id', 'regency_name', 'province_id', 'province_name', 'postal_code', 'is_default'])]
class UserAddress extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
