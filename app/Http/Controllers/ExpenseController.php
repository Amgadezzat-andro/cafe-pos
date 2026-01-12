<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::latest('expense_date')->paginate(20);
        
        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Display daily expense report.
     */
    public function dailyReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now());

        $dailyExpenses = Expense::dailyReport($startDate, $endDate);
        $totalAmount = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $averageAmount = $dailyExpenses->avg('total') ?? 0;

        return view('expenses.reports.daily', compact('dailyExpenses', 'totalAmount', 'averageAmount', 'startDate', 'endDate'));
    }

    /**
     * Display monthly expense report.
     */
    public function monthlyReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $monthlyExpenses = Expense::monthlyReport($year);
        
        // Map month numbers to month names
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];
        
        foreach ($monthlyExpenses as $expense) {
            $expense->month_name = $months[$expense->month - 1] ?? 'Unknown';
        }
        
        $totalAmount = Expense::whereYear('expense_date', $year)->sum('amount');
        $averageAmount = $monthlyExpenses->avg('total') ?? 0;

        return view('expenses.reports.monthly', compact('monthlyExpenses', 'totalAmount', 'averageAmount', 'year', 'months'));
    }
}
