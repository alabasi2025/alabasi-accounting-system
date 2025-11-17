<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\AnalyticalAccount;
use App\Models\Voucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_accounts' => Account::count(),
            'total_journal_entries' => JournalEntry::count(),
            'total_analytical_accounts' => AnalyticalAccount::count(),
            'total_vouchers' => Voucher::count(),
            'pending_journal_entries' => JournalEntry::where('status', 'pending')->count(),
            'pending_vouchers' => Voucher::where('status', 'pending')->count(),
        ];

        $recent_entries = JournalEntry::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_entries'));
    }
}
