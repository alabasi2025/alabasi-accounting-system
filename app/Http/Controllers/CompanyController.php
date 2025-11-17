<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $companies = Company::with('unit')
                           ->withCount('branches')
                           ->orderBy('company_name')
                           ->paginate(20);
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        $units = Unit::where('is_active', true)
                    ->orderBy('unit_name')
                    ->get();
        
        return view('companies.create', compact('units'));
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'company_code' => 'required|string|max:50|unique:companies',
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'director_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $company = Company::create($validated);

        return redirect()->route('companies.index')
                       ->with('success', 'تم إنشاء المؤسسة بنجاح');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        $company->load(['unit', 'branches']);
        
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        $units = Unit::where('is_active', true)
                    ->orderBy('unit_name')
                    ->get();
        
        return view('companies.edit', compact('company', 'units'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'company_code' => 'required|string|max:50|unique:companies,company_code,' . $company->id,
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'director_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $company->update($validated);

        return redirect()->route('companies.index')
                       ->with('success', 'تم تحديث المؤسسة بنجاح');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        // Check if company has branches
        if ($company->branches()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المؤسسة لأنها تحتوي على فروع');
        }

        // Delete logo if exists
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('companies.index')
                       ->with('success', 'تم حذف المؤسسة بنجاح');
    }
}
