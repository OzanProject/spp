<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function siswaDashboard()
    {
        $student = Auth::user()->student;

        if (!$student) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak terhubung dengan data siswa.');
        }

        $invoices = Invoice::where('student_id', $student->id)->get();
        
        $stats = [
            'total_bill' => $invoices->sum('amount'),
            'total_paid' => $invoices->where('status', 'paid')->sum('amount'),
            'total_unpaid' => $invoices->whereIn('status', ['unpaid', 'overdue'])->sum('amount'),
            'count_unpaid' => $invoices->whereIn('status', ['unpaid', 'overdue'])->count(),
        ];

        // Tagihan bulan ini
        $currentMonthInvoice = Invoice::where('student_id', $student->id)
            ->where('month', date('n'))
            ->where('year', date('Y'))
            ->first();

        // Riwayat transaksi terbaru
        $recentPayments = Payment::whereHas('invoice', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact('student', 'stats', 'currentMonthInvoice', 'recentPayments'));
    }
}
