<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAmount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'expense_category_id',
        'amount',
        'effective_date',
    ];

    protected $casts = [
        'amount' => 'integer',
        'effective_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}
