<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Account;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::with(['branch', 'account'])->latest()->paginate(15);
        return view('bank_accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $accounts = Account::where('is_active', true)->get();
        return view('bank_accounts.create', compact('branches', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:bank_accounts',
            'bank_name' => 'required',
            'account_number' => 'required',
            'iban' => 'nullable',
            'branch_id' => 'required|exists:branches,id',
            'account_id' => 'required|exists:accounts,id',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم إضافة الحساب البنكي بنجاح');
    }

    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['branch', 'account', 'vouchers']);
        return view('bank_accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount)
    {
        $branches = Branch::where('is_active', true)->get();
        $accounts = Account::where('is_active', true)->get();
        return view('bank_accounts.edit', compact('bankAccount', 'branches', 'accounts'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'code' => 'required|unique:bank_accounts,code,' . $bankAccount->id,
            'bank_name' => 'required',
            'account_number' => 'required',
            'iban' => 'nullable',
            'branch_id' => 'required|exists:branches,id',
            'account_id' => 'required|exists:accounts,id',
            'is_active' => 'boolean',
            'notes' => 'nullable',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم تحديث الحساب البنكي بنجاح');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم حذف الحساب البنكي بنجاح');
    }
}
