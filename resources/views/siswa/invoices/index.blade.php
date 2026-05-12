@extends('siswa.layouts.master')
@section('title', 'Tagihan Saya')
@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0 text-primary fw-bold">Tagihan Saya</h3>
            <p class="text-muted small">Kelola dan pantau status pembayaran SPP Anda.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.payments.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i> Riwayat
            </a>
            <a href="{{ route('siswa.payments.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bi bi-cloud-upload me-1"></i> Upload Bukti
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="row">
            @forelse($invoices as $invoice)
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100 transition-hover overflow-hidden">
                    {{-- Status Badge on top --}}
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-{{ $invoice->status_color }} shadow-sm px-3 py-2 rounded-pill">
                            {{ strtoupper($invoice->status_label) }}
                        </span>
                    </div>
                    
                    <div class="card-body pt-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light-primary p-3 rounded-circle text-primary me-3">
                                <i class="bi bi-receipt-cutoff fs-3"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">SPP {{ $invoice->month_name }}</h5>
                                <p class="text-muted small mb-0">{{ $invoice->year }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small d-block mb-1">Nominal Tagihan</label>
                            <h3 class="fw-bold text-dark">{{ currency($invoice->amount) }}</h3>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6 border-end">
                                <label class="text-muted small d-block mb-1">Jatuh Tempo</label>
                                <span class="fw-bold small {{ $invoice->status == 'overdue' ? 'text-danger' : 'text-dark' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </span>
                            </div>
                            <div class="col-6 ps-3">
                                <label class="text-muted small d-block mb-1">ID Tagihan</label>
                                <span class="fw-bold small text-dark">#INV-{{ $invoice->id }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($invoice->status == 'unpaid' || $invoice->status == 'overdue' || $invoice->status == 'failed')
                                <a href="{{ route('siswa.payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary">
                                    <i class="bi bi-lightning-charge me-1"></i> Bayar & Upload
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $invoice->id }}">
                                    <i class="bi bi-info-circle me-1"></i> Detail Pembayaran
                                </button>
                            @elseif($invoice->status == 'pending')
                                <button type="button" class="btn btn-warning disabled w-100">
                                    <i class="bi bi-clock me-1"></i> Menunggu Verifikasi
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $invoice->id }}">
                                    <i class="bi bi-image me-1"></i> Lihat Bukti Saya
                                </button>
                            @elseif($invoice->status == 'paid')
                                <a href="{{ route('admin.pdf.invoice', $invoice->id) }}" target="_blank" class="btn btn-success">
                                    <i class="bi bi-download me-1"></i> Download Kwitansi
                                </a>
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $invoice->id }}">
                                    <i class="bi bi-patch-check me-1"></i> Detail Lunas
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Modal Detail --}}
                <div class="modal fade text-left" id="detailModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Rincian Tagihan #{{ $invoice->id }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    <p class="text-muted mb-1 small">Tagihan Periode</p>
                                    <h4 class="fw-bold">{{ $invoice->month_name }} {{ $invoice->year }}</h4>
                                    <h2 class="fw-bold text-primary my-3">{{ currency($invoice->amount) }}</h2>
                                    <span class="badge bg-{{ $invoice->status_color }} px-3 py-2 rounded-pill">{{ strtoupper($invoice->status_label) }}</span>
                                </div>

                                <div class="bg-light rounded p-3 mb-4">
                                    @if($invoice->status == 'paid' && $invoice->payment && $invoice->payment->method == 'gateway')
                                        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-shield-check me-2 text-success"></i>Metode Pembayaran</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted">Status</span>
                                            <span class="badge bg-success px-2 py-1 rounded-pill small">Pembayaran Otomatis</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted">Provider</span>
                                            <span class="fw-bold small text-capitalize">{{ $invoice->payment->bank_name ?? 'Payment Gateway' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">Waktu Bayar</span>
                                            <span class="fw-bold small">{{ $invoice->payment->paid_at ? $invoice->payment->paid_at->format('d M Y, H:i') : '-' }}</span>
                                        </div>
                                    @else
                                        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-bank me-2"></i>Rekening Pembayaran</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted">Bank</span>
                                            <span class="fw-bold small">{{ setting('bank_name') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted">No. Rekening</span>
                                            <span class="fw-bold small">{{ setting('bank_account') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">Atas Nama</span>
                                            <span class="fw-bold small">{{ setting('bank_holder') }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if(($invoice->status == 'pending' || $invoice->status == 'paid') && $invoice->payment && $invoice->payment->method == 'manual')
                                    <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-image me-2"></i>Bukti Pembayaran</h6>
                                    @if($invoice->payment->proof)
                                        <div class="text-center">
                                            <img src="{{ Storage::url('proofs/' . $invoice->payment->proof) }}" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                                            <p class="small text-muted mt-2 mb-0">Diunggah: {{ $invoice->payment->paid_at ? $invoice->payment->paid_at->format('d M Y H:i') : ($invoice->payment->created_at ? $invoice->payment->created_at->format('d M Y H:i') : '-') }}</p>
                                        </div>
                                    @endif
                                @endif

                                @if($invoice->status == 'failed')
                                    <div class="alert alert-danger border-0">
                                        <h6 class="alert-heading fw-bold small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Alasan Penolakan:</h6>
                                        <p class="mb-0 small">{{ $invoice->payment->reject_reason ?? 'Admin tidak memberikan alasan spesifik.' }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer bg-light p-2">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                                @if($invoice->status == 'unpaid' || $invoice->status == 'overdue' || $invoice->status == 'failed')
                                    <a href="{{ route('siswa.payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-primary px-4">Lanjut Bayar</a>
                                @elseif($invoice->status == 'paid')
                                    <a href="{{ route('admin.pdf.invoice', $invoice->id) }}" target="_blank" class="btn btn-sm btn-success px-4">Unduh Kwitansi</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card shadow-sm border-0 py-5">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt text-muted display-1"></i>
                        <h4 class="mt-4 text-muted">Tidak ada tagihan yang ditemukan.</h4>
                        <p class="text-muted">Semua tagihan Anda sudah diselesaikan atau belum diterbitkan.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $invoices->links() }}
        </div>
    </section>
</div>

<style>
    .transition-hover {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .transition-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
</style>
@endsection
