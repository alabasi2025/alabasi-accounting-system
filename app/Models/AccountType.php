<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'nature',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الحسابات
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Scope: الأنواع النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على اسم الطبيعة بالعربية
     */
    public function getNatureNameAttribute()
    {
        return $this->nature === 'debit' ? 'مدين' : 'دائن';
    }
}
