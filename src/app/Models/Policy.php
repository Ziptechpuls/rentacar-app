<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'content',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
