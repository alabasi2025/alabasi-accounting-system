<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Unit;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // تحميل الحسابات بشكل متداخل (5 مستويات)
        $accounts = Account::whereNull('parent_id')
            ->with([
                'children' => function($q) {
                    $q->orderBy('code');
                },
                'children.children' => function($q) {
                    $q->orderBy('code');
                },
                'children.children.children' => function($q) {
                    $q->orderBy('code');
                },
                'children.children.children.children' => function($q) {
                    $q->orderBy('code');
                }
            ])
            ->orderBy('code')
            ->get();
        
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get all account types
        $accountTypes = \App\Models\AccountType::all();
        
        // Get parent accounts
        $parentAccounts = Account::where('is_parent', true)
            ->orderBy('code')
            ->get();
        
        return view('accounts.create', compact('parentAccounts', 'accountTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_parent' => 'boolean',
            'allow_posting' => 'boolean',
            'account_nature' => 'nullable|string|in:general,cash_box,bank,customer,supplier,employee,debtor,creditor',
            'description' => 'nullable|string',
        ]);

        // Set default values
        $validated['is_parent'] = $request->has('is_parent') ? true : false;
        $validated['allow_posting'] = $request->has('allow_posting') ? true : false;
        
        // Add unit_id from session
        $validated['unit_id'] = session('unit_id');

        $account = Account::create($validated);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'تم إضافة الحساب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $account->load(['parent', 'children', 'unit']);
        
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        // Get all accounts for parent selection (excluding current account and its children)
        $parentAccounts = Account::where('is_parent', true)
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();
        
        return view('accounts.edit', compact('account', 'parentAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_parent' => 'boolean',
            'allow_posting' => 'boolean',
            'analytical_type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        // Set default values
        $validated['is_parent'] = $request->has('is_parent') ? true : false;
        $validated['allow_posting'] = $request->has('allow_posting') ? true : false;

        $account->update($validated);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        // Check if account has children
        if ($account->children()->count() > 0) {
            return redirect()
                ->route('accounts.index')
                ->with('error', 'لا يمكن حذف الحساب لأنه يحتوي على حسابات فرعية');
        }
        
        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }
}
