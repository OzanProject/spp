<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimelineController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        $year = $request->get('year', date('Y'));
        
        $invoices = Invoice::where('student_id', $student->id)
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        $months = Invoice::MONTHS;
        $years = range(date('Y') - 1, date('Y') + 1);

        return view('siswa.timeline', compact('invoices', 'months', 'year', 'years'));
    }
}
