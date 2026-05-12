<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', date('Y'));
        $months = \App\Models\Invoice::MONTHS;

        // Pemasukan per bulan untuk tahun ini
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[$m] = Invoice::where('year', $year)
                ->where('month', $m)
                ->where('status', 'paid')
                ->sum('amount');
        }

        // Statistik keseluruhan tahun ini
        $stats = [
            'total_income'  => Invoice::where('year', $year)->where('status', 'paid')->sum('amount'),
            'total_paid'    => Invoice::where('year', $year)->where('status', 'paid')->count(),
            'total_unpaid'  => Invoice::where('year', $year)->where('status', 'unpaid')->count(),
            'total_overdue' => Invoice::where('year', $year)->where('status', 'overdue')->count(),
            'total_student' => Student::count(),
        ];

        // Daftar siswa menunggak
        $debtors = Invoice::with(['student.user', 'student.classRoom'])
            ->where('year', $year)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->latest()
            ->take(10)
            ->get();

        // Daftar siswa lunas
        $paidStudents = Invoice::with(['student.user', 'student.classRoom', 'payment'])
            ->where('year', $year)
            ->where('status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        $years = range(date('Y') - 2, date('Y') + 1);

        return view('backend.reports.index', compact('monthly', 'stats', 'debtors', 'paidStudents', 'year', 'months', 'years'));
    }
}
