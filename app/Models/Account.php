<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'code',
        'name_ar',
        'name_en',
        'account_type_id',
        'parent_id',
        'level',
        'is_parent',
        'allow_posting',
        'currency_id',
        'is_active',
        'opening_balance',
        'opening_balance_type',
        'created_by'
    ];

    protected $casts = [
        'is_parent' => 'boolean',
        'allow_posting' => 'boolean',
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2'
    ];

    // العلاقات
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }
}
