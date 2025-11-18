<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Account;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('account')->latest()->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        // عرض الحسابات من نوع "مورد" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'supplier')
            ->get();
        return view('suppliers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:suppliers',
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'account_id' => 'required|exists:accounts,id',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'tax_number' => 'nullable',
            'commercial_register' => 'nullable',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('account');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // عرض الحسابات من نوع "مورد" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'supplier')
            ->get();
        return view('suppliers.edit', compact('supplier', 'accounts'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'code' => 'required|unique:suppliers,code,' . $supplier->id,
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'account_id' => 'required|exists:accounts,id',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'tax_number' => 'nullable',
            'commercial_register' => 'nullable',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }
}
