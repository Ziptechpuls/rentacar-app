<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expenseAmounts()
    {
        return $this->hasMany(ExpenseAmount::class);
    }

    public function getTypeNameAttribute()
    {
        return $this->type === 'fixed' ? '固定費' : '変動費';
    }
}
