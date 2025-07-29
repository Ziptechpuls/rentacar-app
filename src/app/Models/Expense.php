<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'type',
        'category',
        'date',
        'is_recurring',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    // 固定費かどうか
    public function isFixed(): bool
    {
        return $this->type === 'fixed';
    }

    // 変動費かどうか
    public function isVariable(): bool
    {
        return $this->type === 'variable';
    }

    // カテゴリ名を日本語で取得
    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'rent' => '家賃',
            'utilities' => '光熱費',
            'insurance' => '保険',
            'maintenance' => 'メンテナンス',
            'fuel' => '燃料費',
            'other' => 'その他',
            default => 'その他',
        };
    }

    // タイプ名を日本語で取得
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'fixed' => '固定費',
            'variable' => '変動費',
            default => 'その他',
        };
    }
}
