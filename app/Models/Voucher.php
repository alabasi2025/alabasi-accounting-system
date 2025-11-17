<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'voucher_type',
        'voucher_number',
        'voucher_date',
        'account_id',
        'analytical_account_id',
        'amount',
        'description',
        'reference_number',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function analyticalAccount()
    {
        return $this->belongsTo(AnalyticalAccount::class, 'analytical_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
