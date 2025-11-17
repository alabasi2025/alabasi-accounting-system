<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::with('parent')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();
        
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parent_accounts = Account::where('is_parent', true)
            ->orderBy('code')
            ->get();
        
        return view('accounts.create', compact('parent_accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:accounts',
            'name_ar' => 'required',
            'parent_id' => 'nullable|exists:accounts,id',
            'allow_posting' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'تم إضافة الحساب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
