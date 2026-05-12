@extends('siswa.layouts.master')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-8">
            <h3 class="fw-bold text-primary mb-1">Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! ✨</h3>
            <p class="text-muted">Kelola tagihan SPP dan pantau riwayat pembayaran Anda dengan mudah.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            <div class="d-inline-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-pill border">
                <div class="bg-primary-light rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-calendar-event text-primary small"></i>
                </div>
                <span class="fw-bold small">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Notifikasi Penolakan --}}
    @php
        $latestRejected = \App\Models\Payment::whereHas('invoice', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })->where('status', 'failed')->latest()->first();
    @endphp

    @if($latestRejected && $latestRejected->invoice->status != 'paid' && $latestRejected->invoice->status != 'pending')
        <div class="alert alert-danger border-0 shadow-sm mt-4 animate__animated animate__headShake">
            <div class="d-flex align-items-center p-2">
                <i class="bi bi-exclamation-triangle-fill fs-2 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1 fw-bold text-white">Perlu Tindakan: Pembayaran Ditolak</h6>
                    <p class="mb-2 small opacity-75">Tagihan {{ $latestRejected->invoice->month_name }} ditolak karena: {{ $latestRejected->reject_reason }}</p>
                    <a href="{{ route('siswa.payments.create', ['invoice_id' => $latestRejected->invoice_id]) }}" class="btn btn-sm btn-white text-danger fw-bold rounded-pill">Perbaiki Sekarang</a>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="page-content">
    {{-- Card Statistik Utama --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 transition-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary-light p-3 rounded-3 text-primary">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <span class="badge bg-light-primary text-primary rounded-pill px-2">Total</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Total Tagihan</h6>
                    <h4 class="fw-bold mb-0">{{ currency($stats['total_bill']) }}</h4>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 transition-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-success-light p-3 rounded-3 text-success">
                            <i class="bi bi-check-all fs-4"></i>
                        </div>
                        <span class="badge bg-light-success text-success rounded-pill px-2">Aman</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Sudah Dibayar</h6>
                    <h4 class="fw-bold mb-0">{{ currency($stats['total_paid']) }}</h4>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: {{ $stats['total_bill'] > 0 ? ($stats['total_paid'] / $stats['total_bill'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 transition-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-danger-light p-3 rounded-3 text-danger">
                            <i class="bi bi-exclamation-circle fs-4"></i>
                        </div>
                        <span class="badge bg-light-danger text-danger rounded-pill px-2">Penting</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Belum Dibayar</h6>
                    <h4 class="fw-bold mb-0">{{ currency($stats['total_unpaid']) }}</h4>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-danger" style="width: {{ $stats['total_bill'] > 0 ? ($stats['total_unpaid'] / $stats['total_bill'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 transition-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-warning-light p-3 rounded-3 text-warning">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <span class="badge bg-light-warning text-warning rounded-pill px-2">Bulan</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Sisa Tunggakan</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['count_unpaid'] }} Kali</h4>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: {{ $stats['count_unpaid'] > 0 ? 50 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Tagihan Fokus --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5 bg-primary d-flex flex-column justify-content-center p-5 text-white">
                            <p class="text-white-50 small mb-1">Tagihan Periode</p>
                            <h3 class="fw-bold text-white mb-4">
                                @if($currentMonthInvoice)
                                    SPP {{ $currentMonthInvoice->month_name }} {{ $currentMonthInvoice->year }}
                                @else
                                    Semua Beres!
                                @endif
                            </h3>
                            @if($currentMonthInvoice)
                                <div class="bg-white bg-opacity-10 rounded p-3 mb-4">
                                    <h2 class="fw-bold text-white mb-0">{{ currency($currentMonthInvoice->amount) }}</h2>
                                    <small class="text-white-50">Selesaikan sebelum {{ $currentMonthInvoice->due_date->translatedFormat('d M Y') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-7 p-5 d-flex flex-column justify-content-center bg-white">
                            @if($currentMonthInvoice)
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-2">Status Pembayaran</h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light-{{ $currentMonthInvoice->status_color }} text-{{ $currentMonthInvoice->status_color }} fs-6 px-3 py-2 rounded-pill me-2">
                                            {{ strtoupper($currentMonthInvoice->status_label) }}
                                        </span>
                                        @if($currentMonthInvoice->status == 'paid')
                                            <span class="text-success small fw-bold"><i class="bi bi-patch-check me-1"></i> Terverifikasi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    @if($currentMonthInvoice->status == 'paid')
                                        <a href="{{ route('admin.pdf.invoice', $currentMonthInvoice->id) }}" target="_blank" class="btn btn-success btn-lg shadow-sm rounded-pill">
                                            <i class="bi bi-download me-2"></i> Unduh Kwitansi
                                        </a>
                                    @elseif($currentMonthInvoice->status == 'pending')
                                        <button class="btn btn-warning btn-lg disabled rounded-pill">
                                            <i class="bi bi-hourglass-split me-2"></i> Menunggu Verifikasi
                                        </button>
                                    @else
                                        <a href="{{ route('siswa.payments.create', ['invoice_id' => $currentMonthInvoice->id]) }}" class="btn btn-primary btn-lg shadow-sm rounded-pill">
                                            <i class="bi bi-lightning-charge-fill me-2"></i> Bayar Sekarang
                                        </a>
                                        <div class="text-center mt-2">
                                            <a href="{{ route('siswa.invoices.index') }}" class="text-muted small">Lihat Detail Tagihan Lainnya</a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="bg-light-success rounded-circle p-4 d-inline-flex mb-3">
                                        <i class="bi bi-shield-check display-4 text-success"></i>
                                    </div>
                                    <h5 class="fw-bold">Tidak ada tagihan tertunda</h5>
                                    <p class="text-muted small">Terima kasih telah membayar tepat waktu!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat --}}
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Transaksi Terakhir</h5>
                    <a href="{{ route('siswa.payments.index') }}" class="btn btn-sm btn-light-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="ps-4">Periode</th>
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th class="pe-4 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded me-2">
                                                <i class="bi bi-receipt text-primary"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $payment->invoice->month_name }} {{ $payment->invoice->year }}</span>
                                        </div>
                                    </td>
                                    <td class="small">{{ $payment->paid_at->translatedFormat('d M Y') }}</td>
                                    <td class="fw-bold text-dark">{{ currency($payment->amount) }}</td>
                                    <td class="pe-4 text-end">
                                        <span class="badge bg-light-{{ $payment->status == 'success' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }} text-{{ $payment->status == 'success' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }} px-3 py-2 rounded-pill small">
                                            {{ $payment->status == 'success' ? 'Berhasil' : ($payment->status == 'pending' ? 'Pending' : 'Ditolak') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted opacity-50">Belum ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white py-4 px-4 border-0">
                    <h5 class="fw-bold mb-0">Pintasan Cepat</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('siswa.invoices.index') }}" class="quick-action-card bg-light-primary text-primary">
                                <i class="bi bi-receipt fs-3"></i>
                                <span>Tagihan</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('siswa.payments.create') }}" class="quick-action-card bg-light-success text-success">
                                <i class="bi bi-upc-scan fs-3"></i>
                                <span>Bayar SPP</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('siswa.notifications') }}" class="quick-action-card bg-light-warning text-warning">
                                <i class="bi bi-bell fs-3"></i>
                                <span>Pesan</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('siswa.profile.edit') }}" class="quick-action-card bg-light-info text-info">
                                <i class="bi bi-person-badge fs-3"></i>
                                <span>Profil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Help Center --}}
            <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden">
                <div class="card-body p-4 position-relative" style="min-height: 160px;">
                    <div class="help-card-icon" style="z-index: 0;">
                        <i class="bi bi-question-circle-fill"></i>
                    </div>
                    <div class="position-relative" style="z-index: 2;">
                        <h5 class="fw-bold mb-3 text-white">Butuh Bantuan?</h5>
                        <p class="small text-white-50 mb-4">Jika Anda mengalami kendala pembayaran atau perbedaan data, silakan hubungi bagian keuangan.</p>
                        <a href="https://wa.me/{{ setting('school_phone') }}" target="_blank" class="btn btn-white text-primary w-100 rounded-pill fw-extrabold shadow-sm">
                            <i class="bi bi-whatsapp me-2"></i> WhatsApp Bendahara
                        </a>
                    </div>
                </div>
            </div>

            {{-- Rekening --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bank me-2 text-primary"></i>Info Rekening Sekolah</h6>
                    <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-muted d-block">Bank</small>
                                <span class="fw-bold text-dark">{{ setting('bank_name', 'BANK TRANSFER') }}</span>
                            </div>
                            <img src="https://img.icons8.com/color/48/000000/visa.png" style="height: 20px; opacity: 0.5;">
                        </div>
                        <div class="mt-3">
                            <small class="text-muted d-block">No. Rekening</small>
                            <h5 class="fw-bold text-primary mb-0 letter-spacing-1">{{ setting('bank_account', '-') }}</h5>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted d-block">Atas Nama</small>
                            <span class="small fw-bold text-dark">{{ setting('bank_holder', setting('school_name')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(67, 94, 190, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }
    .bg-light-info { background-color: rgba(13, 202, 240, 0.1); }
    
    .btn-white { 
        background-color: #ffffff !important; 
        color: var(--bs-primary) !important; 
        border: none !important; 
        font-weight: 800 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .btn-white:hover { 
        background-color: #f8f9fa !important; 
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .transition-up { transition: transform 0.3s ease; }
    .transition-up:hover { transform: translateY(-5px); }
    
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .quick-action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 0.5rem;
        border-radius: 1.25rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        min-height: 120px;
        width: 100%;
        text-align: center;
    }
    .quick-action-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: rgba(0,0,0,0.05);
    }
    .quick-action-card i { 
        font-size: 2.2rem; 
        margin-bottom: 0.75rem;
        line-height: 1;
    }
    .quick-action-card span { 
        font-size: 0.8rem; 
        font-weight: 800;
        display: block;
        line-height: 1.2;
    }
    
    .help-card-icon {
        position: absolute;
        bottom: -20px;
        right: -10px;
        font-size: 7rem;
        opacity: 0.15;
        transform: rotate(-15deg);
        pointer-events: none;
    }
    
    .border-dashed { border: 2px dashed #dee2e6 !important; }
</style>
@endsection

