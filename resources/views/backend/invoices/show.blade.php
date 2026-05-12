@extends('backend.layouts.master')
@section('title', 'Detail Tagihan')
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Tagihan</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Tagihan SPP</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Informasi Tagihan</h5>
                        <span class="badge bg-{{ $invoice->status_color }} fs-6">{{ $invoice->status_label }}</span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr><td class="text-muted" width="40%">Nama Siswa</td><td class="fw-semibold">{{ $invoice->student->user->name }}</td></tr>
                            <tr><td class="text-muted">NIS</td><td>{{ $invoice->student->nis }}</td></tr>
                            <tr><td class="text-muted">Kelas</td><td>{{ $invoice->student->classRoom->name ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Periode</td><td class="fw-semibold">{{ $invoice->month_name }} {{ $invoice->year }}</td></tr>
                            <tr><td class="text-muted">Nominal</td><td class="fw-bold fs-5 text-primary">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td></tr>
                            <tr>
                                <td class="text-muted">Jatuh Tempo</td>
                                <td class="{{ $invoice->status !== 'paid' && now()->gt($invoice->due_date) ? 'text-danger fw-bold' : '' }}">
                                    {{ $invoice->due_date->format('d F Y') }}
                                </td>
                            </tr>
                            @if($invoice->notes)
                            <tr><td class="text-muted">Catatan</td><td>{{ $invoice->notes }}</td></tr>
                            @endif
                        </table>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i> Edit</a>
                            @if($invoice->status !== 'paid')
                            <form action="{{ route('admin.invoices.markPaid', $invoice->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Tandai tagihan ini sebagai LUNAS?')">
                                    <i class="bi bi-check-lg me-1"></i> Tandai Lunas
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary ms-auto"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Status & Bukti Bayar</h5>
                        @if($invoice->payment)
                            <span class="badge bg-{{ $invoice->payment->status == 'success' ? 'success' : ($invoice->payment->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ strtoupper($invoice->payment->status) }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($invoice->payment)
                            <div class="list-group list-group-flush mb-3">
                                <div class="list-group-item px-0 d-flex justify-content-between">
                                    <span class="text-muted small">Waktu Upload</span>
                                    <span class="fw-bold small">{{ $invoice->payment->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="list-group-item px-0 d-flex justify-content-between">
                                    <span class="text-muted small">Metode</span>
                                    <span class="fw-bold small text-capitalize">{{ $invoice->payment->method }}</span>
                                </div>
                                @if($invoice->payment->note)
                                <div class="list-group-item px-0">
                                    <span class="text-muted small d-block mb-1">Catatan:</span>
                                    <div class="p-2 bg-light rounded small italic">{{ $invoice->payment->note }}</div>
                                </div>
                                @endif
                            </div>

                            @if($invoice->payment->proof)
                                <p class="text-muted small mb-2">BUKTI TRANSFER:</p>
                                <a href="{{ Storage::url('proofs/' . $invoice->payment->proof) }}" target="_blank">
                                    <img src="{{ Storage::url('proofs/' . $invoice->payment->proof) }}" class="img-fluid rounded shadow-sm border w-100" alt="Bukti Transfer">
                                </a>
                                <small class="text-center d-block mt-2 text-muted italic">*Klik gambar untuk memperbesar</small>
                            @else
                                <div class="alert alert-light-warning small">
                                    <i class="bi bi-exclamation-circle me-1"></i> Tagihan lunas via input admin (tanpa bukti upload).
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                                Belum ada pembayaran atau unggahan bukti.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
