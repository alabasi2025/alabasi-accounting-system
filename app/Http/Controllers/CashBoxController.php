<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\Branch;
use App\Models\Account;
use Illuminate\Http\Request;

class CashBoxController extends Controller
{
    public function index()
    {
        $cashBoxes = CashBox::with(['branch', 'account'])->latest()->paginate(15);
        return view('cashboxes.index', compact('cashBoxes'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        // عرض الحسابات من نوع "صندوق" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'cash_box')
            ->get();
        return view('cashboxes.create', compact('branches', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:cash_boxes',
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'branch_id' => 'required|exists:branches,id',
            'account_id' => 'required|exists:accounts,id',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];

        CashBox::create($validated);

        return redirect()->route('cashboxes.index')
            ->with('success', 'تم إضافة الصندوق بنجاح');
    }

    public function show(CashBox $cashbox)
    {
        $cashbox->load(['branch', 'account', 'vouchers']);
        return view('cashboxes.show', compact('cashbox'));
    }

    public function edit(CashBox $cashbox)
    {
        $branches = Branch::where('is_active', true)->get();
        // عرض الحسابات من نوع "صندوق" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'cash_box')
            ->get();
        return view('cashboxes.edit', compact('cashbox', 'branches', 'accounts'));
    }

    public function update(Request $request, CashBox $cashbox)
    {
        $validated = $request->validate([
            'code' => 'required|unique:cash_boxes,code,' . $cashbox->id,
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'branch_id' => 'required|exists:branches,id',
            'account_id' => 'required|exists:accounts,id',
            'is_active' => 'boolean',
            'notes' => 'nullable',
        ]);

        $cashbox->update($validated);

        return redirect()->route('cashboxes.index')
            ->with('success', 'تم تحديث الصندوق بنجاح');
    }

    public function destroy(CashBox $cashbox)
    {
        $cashbox->delete();
        return redirect()->route('cashboxes.index')
            ->with('success', 'تم حذف الصندوق بنجاح');
    }
}
