<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
