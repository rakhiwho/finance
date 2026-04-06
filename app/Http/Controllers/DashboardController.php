<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get overall financial summary
     * Returns total income, total expense, and net balance
     */
    public function summary()
    {
        $income = FinancialRecord::where('type', 'income')->sum('amount');
        $expense = FinancialRecord::where('type', 'expense')->sum('amount');

        return response()->json([
            'total_income' => $income,
            'total_expense' => $expense,
            'net_balance'  => $income - $expense,
        ]);
    }

    /**
     * Get total amount grouped by category
     * Useful for pie charts or category breakdown
     */
    public function categoryTotals()
    {
        $data = FinancialRecord::select('category')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')            
            ->get();

        return response()->json($data);
    }

    /**
     * Get monthly financial trends based on the 'date' field
     * Works with your current date format: DD-MM-YYYY (e.g., 06-04-2026)
     * Returns income, expense, and total for each month
     */
    public function trends()
    {
        return response()->json(
            FinancialRecord::selectRaw("
                substr(date, 4, 2) AS month,                    -- Extract month (MM) from DD-MM-YYYY
                CASE substr(date, 4, 2)
                    WHEN '01' THEN 'Jan' WHEN '02' THEN 'Feb' WHEN '03' THEN 'Mar'
                    WHEN '04' THEN 'Apr' WHEN '05' THEN 'May' WHEN '06' THEN 'Jun'
                    WHEN '07' THEN 'Jul' WHEN '08' THEN 'Aug' WHEN '09' THEN 'Sep'
                    WHEN '10' THEN 'Oct' WHEN '11' THEN 'Nov' WHEN '12' THEN 'Dec'
                END AS month_name,
                substr(date, 7, 4) AS year,                     -- Extract year (YYYY)
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS expense,
                SUM(amount) AS total
            ")
            ->whereNotNull('date')                               
            ->groupByRaw('month, month_name, year')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
        );
    }

    /**
     * Get the 5 most recent financial records
     * Ordered by 'date' field first (your entry date), then by creation time
     */
    public function recent()
    {
        $records = FinancialRecord::select([
                'id',
                'user_id',
                'amount',
                'type',
                'category',
                'date',
                'notes'
            ])
            ->orderBy('date', 'desc')       
            ->orderBy('created_at', 'desc')  
            ->take(5)
            ->get();

        return response()->json($records);
    }
}