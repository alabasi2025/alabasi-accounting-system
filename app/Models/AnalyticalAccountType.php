<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticalAccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
