<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'branch_name',
        'address',
        'phone',
        'email',
        'manager_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}
