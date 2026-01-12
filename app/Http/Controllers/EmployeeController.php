<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(15);
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
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0|decimal:0,2',
            'hire_date' => 'required|date|before_or_equal:today',
            'system_role' => 'required|in:admin,cashier',
            'create_account' => 'nullable|boolean',
        ]);

        $employee = new Employee();
        $employee->name = $validated['name'];
        $employee->role = $validated['role'];
        $employee->salary = $validated['salary'];
        $employee->hire_date = $validated['hire_date'];
        $employee->system_role = $validated['system_role'];

        // Create user account if requested
        if ($request->has('create_account') && $validated['email'] && $validated['password']) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => Role::from($validated['system_role']),
            ]);
            $employee->user_id = $user->id;
        }

        $employee->save();

        $message = $request->has('create_account') ? 'Employee and account created successfully.' : 'Employee created successfully.';
        return redirect()->route('employees.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('user');
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');
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

        // Update related user role if exists
        if ($employee->user) {
            $employee->user->update(['role' => Role::from($validated['system_role'])]);
        }

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        
        // Delete associated user account if exists
        if ($employee->user) {
            $employee->user->delete();
        }
        
        $employee->delete();

        return redirect()->route('employees.index')->with('success', "Employee '{$name}' and associated account deleted successfully.");
    }

    /**
     * Create account for existing employee
     */
    public function createAccount(Request $request, Employee $employee)
    {
        if ($employee->user) {
            return redirect()->route('employees.show', $employee)->with('error', 'Employee already has an account.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $employee->name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => Role::from($employee->system_role),
        ]);

        $employee->update(['user_id' => $user->id]);

        return redirect()->route('employees.show', $employee)->with('success', 'Account created successfully for ' . $employee->name);
    }
}
