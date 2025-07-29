<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'logo_path',
        'theme_color',
        'is_active',
        'contract_start_date',
        'contract_end_date',
        'contract_plan',
        'contract_memo',
        'tel',
        'email',
        'address',
        'business_hours',
        'access',
        'map_iframe',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'contract_start_date' => 'datetime',
        'contract_end_date' => 'datetime',
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    /**
     * サブドメインから会社を取得
     */
    public static function findBySubdomain(string $subdomain)
    {
        return static::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();
    }

    /**
     * 契約が有効かどうかを確認
     */
    public function hasValidContract(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->contract_start_date || !$this->contract_end_date) {
            return false;
        }

        $now = now();
        return $now->between($this->contract_start_date, $this->contract_end_date);
    }
} 