<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalSiswa = \App\Models\Student::count();
        
        $pemasukanBulanIni = \App\Models\Invoice::where('month', date('n'))
            ->where('year', date('Y'))->where('status', 'paid')->sum('amount');
            
        $lunasBulanIni = \App\Models\Invoice::where('month', date('n'))
            ->where('year', date('Y'))->where('status', 'paid')->count();
            
        $menunggak = \App\Models\Invoice::whereIn('status', ['unpaid', 'overdue'])->count();

        // Chart data
        $year = date('Y');
        $monthlyIncome = array_fill(1, 12, 0);
        $paidInvoicesThisYear = \App\Models\Invoice::where('year', $year)
            ->where('status', 'paid')->get();
            
        foreach ($paidInvoicesThisYear as $inv) {
            $monthlyIncome[$inv->month] += $inv->amount;
        }
        $chartData = array_values($monthlyIncome);

        // Recent Payments
        $recentPayments = \App\Models\Payment::with(['invoice.student.user'])
            ->latest('paid_at')
            ->take(5)
            ->get();

        return view('backend.dashboard.index', compact(
            'totalSiswa', 'pemasukanBulanIni', 'lunasBulanIni', 'menunggak', 'chartData', 'recentPayments', 'year'
        ));
    }
}
