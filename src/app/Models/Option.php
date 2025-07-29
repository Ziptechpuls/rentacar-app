<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_quantity',
        'image_path',
        'price_type',
        'sort_order',
    ];

    protected $casts = [
        'is_quantity' => 'boolean',
    ];

    /**
     * デフォルトの並び順
     */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        });
    }

    public function getPriceTypeTextAttribute()
    {
        return [
            'per_piece' => '1個あたり',
            'per_day' => '1日あたり',
        ][$this->price_type] ?? '不明';
    }
}
