<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Account;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('account')->latest()->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        // عرض الحسابات من نوع "موظف" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'employee')
            ->get();
        return view('employees.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:employees',
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'account_id' => 'required|exists:accounts,id',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'national_id' => 'nullable',
            'job_title' => 'nullable',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'notes' => 'nullable',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function show(Employee $employee)
    {
        $employee->load('account');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // عرض الحسابات من نوع "موظف" فقط
        $accounts = Account::where('is_active', true)
            ->where('account_nature', 'employee')
            ->get();
        return view('employees.edit', compact('employee', 'accounts'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'code' => 'required|unique:employees,code,' . $employee->id,
            'name_ar' => 'required',
            'name_en' => 'nullable',
            'account_id' => 'required|exists:accounts,id',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'national_id' => 'nullable',
            'job_title' => 'nullable',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'is_active' => 'boolean',
            'notes' => 'nullable',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'تم تحديث الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }
}
