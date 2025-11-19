<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get account types
        $query = AccountType::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nature')) {
            $query->where('nature', $request->nature);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $accountTypes = $query->orderBy('code')->paginate(15);

        return view('account-types.index', compact('accountTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('account-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:account_types,code',
            'name' => 'required|string|max:255',
            'nature' => 'required|in:debit,credit',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        AccountType::create($validated);

        return redirect()->route('account-types.index')
            ->with('success', 'تم إضافة نوع الحساب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountType $accountType)
    {
        return view('account-types.show', compact('accountType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountType $accountType)
    {
        return view('account-types.edit', compact('accountType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountType $accountType)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:account_types,code,' . $accountType->id,
            'name' => 'required|string|max:255',
            'nature' => 'required|in:debit,credit',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $accountType->update($validated);

        return redirect()->route('account-types.index')
            ->with('success', 'تم تحديث نوع الحساب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountType $accountType)
    {
        // Check if there are accounts using this type
        if ($accountType->accounts()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف نوع الحساب لأنه مرتبط بحسابات موجودة');
        }

        $accountType->delete();

        return redirect()->route('account-types.index')
            ->with('success', 'تم حذف نوع الحساب بنجاح');
    }
}
