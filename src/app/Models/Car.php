<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Car extends Model
{
    protected $fillable = [
        'name',
        'type',
        'capacity',
        'price',
        'transmission',
        'smoking_preference',
        'has_bluetooth',
        'has_back_monitor',
        'has_navigation',
        'has_etc',
        'description',
        'is_public',
        'car_model_id',
        'car_number',
        'color',
        'car_vin',
        'passenger',
        'store_id',
        'company_id',
        'inspection_date',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * 利用可能な期間に空いている車両だけ取得するスコープ
     */
    public function scopeAvailableBetween($query, $start, $end)
    {
        return $query->whereDoesntHave('reservations', function ($q) use ($start, $end) {
            $q->where(function ($q2) use ($start, $end) {
                $q2->where('start_datetime', '<', $end)
                   ->where('end_datetime', '>', $start);
            });
        });
    }

    /**
     * 公開されている車両のみを取得するスコープ
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * 特定の会社の車両のみを取得するスコープ
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * 禁煙・喫煙のラベル取得アクセサ
     */
    protected function smokingPreferenceLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->smoking_preference === 'non-smoking' ? '🚭 禁煙' : '🚬 喫煙可',
        );
    }

    /**
     * 装備リストを配列で取得するアクセサ
     */
    protected function equipmentList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $list = [];

                if ($this->has_bluetooth) {
                    $list[] = ['icon' => '🎵', 'label' => 'Bluetooth'];
                }
                if ($this->has_back_monitor) {
                    $list[] = ['icon' => '📹', 'label' => 'バックモニター'];
                }
                if ($this->has_navigation) {
                    $list[] = ['icon' => '🗺', 'label' => 'ナビ付き'];
                }
                if ($this->has_etc) {
                    $list[] = ['icon' => '💳', 'label' => 'ETC車載器搭載'];
                }

                return $list;
            }
        );
    }
}




