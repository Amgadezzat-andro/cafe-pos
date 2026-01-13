<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'amount',
        'expense_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get today's total expenses.
     */
    public static function todayTotal()
    {
        return self::whereDate('expense_date', today())->sum('amount');
    }

    /**
     * Get this month's total expenses.
     */
    public static function monthTotal($date = null)
    {
        $date = $date ?? now();
        return self::whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->sum('amount');
    }

    /**
     * Get this year's total expenses.
     */
    public static function yearTotal($year = null)
    {
        $year = $year ?? now()->year;
        return self::whereYear('expense_date', $year)->sum('amount');
    }

    /**
     * Get daily expenses for a date range.
     */
    public static function dailyReport($startDate, $endDate)
    {
        return self::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('expense_date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('expense_date')
            ->orderByDesc('expense_date')
            ->get();
    }

    /**
     * Get monthly expenses for a year.
     */
    public static function monthlyReport($year = null)
    {
        $year = $year ?? now()->year;
        return self::whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as month, COUNT(*) as count, SUM(amount) as total')
            ->groupByRaw('MONTH(expense_date)')
            ->orderByRaw('MONTH(expense_date) DESC')
            ->get();
    }
}
