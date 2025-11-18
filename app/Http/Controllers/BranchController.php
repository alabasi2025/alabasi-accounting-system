<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Unit;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        
        $branches = Branch::with(['company', 'unit'])
            ->when($selectedCompanyId, function($query) use ($selectedCompanyId) {
                return $query->where('company_id', $selectedCompanyId);
            })
            ->orderBy('branch_code')
            ->get();
            
        $companies = Company::all();
        $units = Unit::all();
        
        return view('branches.index', compact('branches', 'companies', 'units'));
    }

    public function create()
    {
        $companies = Company::all();
        $units = Unit::all();
        
        return view('branches.create', compact('companies', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'unit_id' => 'nullable|exists:units,id',
            'branch_code' => 'required|string|max:50|unique:branches',
            'branch_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'تم إضافة الفرع بنجاح');
    }

    public function show(Branch $branch)
    {
        $branch->load(['company', 'unit']);
        
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $companies = Company::all();
        $units = Unit::all();
        
        return view('branches.edit', compact('branch', 'companies', 'units'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'unit_id' => 'nullable|exists:units,id',
            'branch_code' => 'required|string|max:50|unique:branches,branch_code,' . $branch->id,
            'branch_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'تم تحديث الفرع بنجاح');
    }

    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();
            
            return redirect()->route('branches.index')
                ->with('success', 'تم حذف الفرع بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('branches.index')
                ->with('error', 'لا يمكن حذف الفرع لوجود بيانات مرتبطة به');
        }
    }
}
