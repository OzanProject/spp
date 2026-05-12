<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $invoices = Invoice::where('student_id', $student->id)
            ->with('payment')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('siswa.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        // Ensure student can only see their own invoice
        if ($invoice->student_id !== Auth::user()->student->id) {
            abort(403);
        }

        return view('siswa.invoices.show', compact('invoice'));
    }
}
