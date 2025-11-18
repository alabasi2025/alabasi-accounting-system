<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Account;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('account')->latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        // عرض الحسابات من نوع "عميل" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'customer')
            ->get();
        return view('customers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers',
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

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Customer $customer)
    {
        $customer->load('account');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        // عرض الحسابات من نوع "عميل" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'customer')
            ->get();
        return view('customers.edit', compact('customer', 'accounts'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers,code,' . $customer->id,
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

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
