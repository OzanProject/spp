<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student.user', 'student.classRoom'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by class
        if ($request->filled('class_room_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_room_id', $request->class_room_id));
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total'   => Invoice::count(),
            'paid'    => Invoice::where('status', 'paid')->count(),
            'unpaid'  => Invoice::where('status', 'unpaid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'pending' => Invoice::where('status', 'pending')->count(),
        ];

        $classes = \App\Models\ClassRoom::all();

        return view('backend.invoices.index', compact('invoices', 'stats', 'classes'));
    }

    public function create()
    {
        $students = Student::with(['user', 'classRoom'])->get();
        $months   = Invoice::MONTHS;
        $years    = range(date('Y'), date('Y') + 2);

        return view('backend.invoices.create', compact('students', 'months', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'month'      => 'required|integer|between:1,12',
            'year'       => 'required|integer|min:2020',
            'amount'     => 'required|integer|min:1',
            'due_date'   => 'required|date',
        ], [
            'student_id.required' => 'Siswa wajib dipilih.',
            'amount.required'     => 'Nominal tagihan wajib diisi.',
            'due_date.required'   => 'Tanggal jatuh tempo wajib diisi.',
        ]);

        // Cek duplikat
        $exists = Invoice::where('student_id', $request->student_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['month' => 'Tagihan untuk siswa ini pada bulan dan tahun tersebut sudah ada.']);
        }

        Invoice::create([
            'student_id' => $request->student_id,
            'month'      => $request->month,
            'year'       => $request->year,
            'amount'     => $request->amount,
            'due_date'   => $request->due_date,
            'status'     => 'unpaid',
            'notes'      => $request->notes,
        ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student.user', 'student.classRoom', 'payment']);
        return view('backend.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $students = Student::with(['user', 'classRoom'])->get();
        $months   = Invoice::MONTHS;
        $years    = range(2020, date('Y') + 2);

        return view('backend.invoices.edit', compact('invoice', 'students', 'months', 'years'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount'   => 'required|integer|min:1',
            'due_date' => 'required|date',
            'status'   => 'required|in:unpaid,paid,overdue',
        ]);

        $invoice->update([
            'amount'   => $request->amount,
            'due_date' => $request->due_date,
            'status'   => $request->status,
            'notes'    => $request->notes,
        ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    // Tandai lunas secara manual
    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        $payment = \App\Models\Payment::updateOrCreate(
            ['invoice_id' => $invoice->id],
            ['paid_at' => now(), 'status' => 'success', 'note' => 'Dikonfirmasi manual oleh admin']
        );

        // Send Notification
        $invoice->student->user->notify(new \App\Notifications\PaymentSuccessful($invoice, $payment));

        // Send WhatsApp Notification
        if ($invoice->student->parent_phone) {
            $msg = "*PEMBAYARAN BERHASIL (MANUAL)*\n\n";
            $msg .= "Halo " . $invoice->student->user->name . ",\n";
            $msg .= "Tagihan SPP *" . $invoice->month_name . " " . $invoice->year . "* sebesar *" . currency($invoice->amount) . "* telah ditandai LUNAS oleh Admin.\n\n";
            $msg .= "Terima kasih.\n";
            $msg .= "_" . setting('school_name') . "_";
            
            \App\Services\WhatsappService::send($invoice->student->parent_phone, $msg);
        }

        return back()->with('success', 'Tagihan berhasil ditandai lunas.');
    }

    // Generate tagihan massal untuk 1 bulan
    public function generateBulk(Request $request)
    {
        $request->validate([
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2020',
            'amount'   => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]);

        $students = Student::all();
        $created  = 0;
        $skipped  = 0;

        foreach ($students as $student) {
            $exists = Invoice::where('student_id', $student->id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->exists();

            if (!$exists) {
                Invoice::create([
                    'student_id' => $student->id,
                    'month'      => $request->month,
                    'year'       => $request->year,
                    'amount'     => $request->amount,
                    'due_date'   => $request->due_date,
                    'status'     => 'unpaid',
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', "{$created} tagihan berhasil dibuat. {$skipped} tagihan dilewati (sudah ada).");
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data untuk dihapus.');
        }

        Invoice::whereIn('id', $ids)->delete();

        return redirect()->route('admin.invoices.index')->with('success', count($ids) . ' data tagihan berhasil dihapus.');
    }
}
