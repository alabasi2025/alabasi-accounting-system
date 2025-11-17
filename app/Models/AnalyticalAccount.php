<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticalAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'account_id',
        'code',
        'name_ar',
        'name_en',
        'account_type',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'opening_balance',
        'opening_balance_type',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
