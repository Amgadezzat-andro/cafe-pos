<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::latest()->paginate(15);
        $totalEmployees = Employee::count();
        $totalPayroll = Employee::totalPayroll();
        $avgSalary = Employee::avgSalary();

        return view('employees.index', compact('employees', 'totalEmployees', 'totalPayroll', 'avgSalary'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $systemRoles = ['admin', 'cashier'];
        return view('employees.create', compact('systemRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0|decimal:0,2',
            'hire_date' => 'required|date|before_or_equal:today',
            'system_role' => 'required|in:admin,cashier',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $systemRoles = ['admin', 'cashier'];
        return view('employees.edit', compact('employee', 'systemRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0|decimal:0,2',
            'hire_date' => 'required|date|before_or_equal:today',
            'system_role' => 'required|in:admin,cashier',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->delete();

        return redirect()->route('employees.index')->with('success', "Employee '{$name}' deleted successfully.");
    }
}
