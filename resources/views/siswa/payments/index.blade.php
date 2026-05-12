@extends('siswa.layouts.master')
@section('title', 'Riwayat Pembayaran')
@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0 text-primary fw-bold">Riwayat Pembayaran</h3>
            <p class="text-subtitle text-muted">Pantau status verifikasi dan unduh kwitansi Anda.</p>
        </div>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="page-content">
    <section class="section">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading mb-0 fw-bold">Pembayaran Berhasil!</h6>
                        <p class="mb-0 small">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Periode Tagihan</th>
                                <th class="py-3">Waktu Upload</th>
                                <th class="py-3">Nominal</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light-primary p-2 rounded me-3 text-primary">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">SPP {{ $payment->invoice->month_name }} {{ $payment->invoice->year }}</h6>
                                            <small class="text-muted">Inv #{{ $payment->invoice_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold small">{{ $payment->created_at?->translatedFormat('d M Y') ?? '-' }}</div>
                                    <div class="text-muted small">{{ $payment->created_at?->format('H:i') ?? '-' }} WIB</div>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ currency($payment->amount) }}</h6>
                                    <small class="text-muted small">{{ $payment->bank_name ?? '-' }} a.n {{ $payment->sender_name ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($payment->status == 'success')
                                        <span class="badge bg-light-success text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> BERHASIL</span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge bg-light-warning text-warning px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i> VERIFIKASI</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> DITOLAK</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#proofModal{{ $payment->id }}" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($payment->status == 'success')
                                            <a href="{{ route('admin.pdf.invoice', $payment->invoice_id) }}" target="_blank" class="btn btn-sm btn-light-success" title="Download Kwitansi">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    {{-- Modal Bukti --}}
                                    <div class="modal fade text-left" id="proofModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white">Detail Pembayaran</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <div class="p-3 text-center border-bottom bg-light">
                                                        <h6 class="text-muted small mb-1">Status Pembayaran</h6>
                                                        @if($payment->status == 'success')
                                                            <div class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> TERVERIFIKASI</div>
                                                        @elseif($payment->status == 'pending')
                                                            <div class="text-warning fw-bold"><i class="bi bi-clock-fill me-1"></i> SEDANG DIVERIFIKASI</div>
                                                        @else
                                                            <div class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i> PEMBAYARAN DITOLAK</div>
                                                        @endif
                                                    </div>
                                                    <div class="p-4">
                                                        @if($payment->method == 'gateway')
                                                            <div class="text-center mb-4 py-3 bg-light rounded border-dashed">
                                                                <i class="bi bi-shield-check-fill text-success display-4"></i>
                                                                <h6 class="mt-2 fw-bold">Pembayaran Gateway Otomatis</h6>
                                                                <p class="text-muted small">Transaksi ini diverifikasi secara instan oleh sistem.</p>
                                                            </div>
                                                        @else
                                                            <div class="text-center mb-4">
                                                                <img src="{{ Storage::url('proofs/' . $payment->proof) }}" class="img-fluid rounded shadow-sm border w-100" style="max-height: 300px; object-fit: contain;" alt="Bukti Transfer">
                                                                <p class="text-muted small mt-2">File: {{ $payment->proof }}</p>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Metode Pembayaran</small>
                                                                <span class="badge bg-{{ $payment->method == 'gateway' ? 'success' : 'info' }} text-capitalize px-2 py-1 small">
                                                                    {{ $payment->method == 'gateway' ? 'Otomatis' : 'Manual Transfer' }}
                                                                </span>
                                                            </div>
                                                            <div class="col-6 text-end">
                                                                <small class="text-muted d-block">Waktu Transaksi</small>
                                                                <span class="fw-bold small">{{ $payment->paid_at ? $payment->paid_at->translatedFormat('d M Y, H:i') : ($payment->created_at?->translatedFormat('d M Y, H:i') ?? '-') }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Provider/Bank</small>
                                                                <span class="fw-bold small">{{ $payment->bank_name ?? '-' }}</span>
                                                            </div>
                                                            <div class="col-6 text-end">
                                                                <small class="text-muted d-block">Nama Pengirim</small>
                                                                <span class="fw-bold small">{{ $payment->sender_name ?? '-' }}</span>
                                                            </div>
                                                            @if($payment->note)
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Catatan Sistem</small>
                                                                <div class="p-2 bg-light rounded small font-monospace">{{ $payment->note }}</div>
                                                            </div>
                                                            @endif
                                                            <div class="col-12">
                                                                <div class="bg-light p-3 rounded">
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="small text-muted">Total Pembayaran</span>
                                                                        <span class="fw-bold text-primary">{{ currency($payment->amount) }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if($payment->status == 'failed' && $payment->reject_reason)
                                                            <div class="alert alert-danger mt-4 mb-0 border-0 shadow-sm">
                                                                <h6 class="alert-heading fw-bold small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Alasan Penolakan:</h6>
                                                                <p class="mb-0 small">{{ $payment->reject_reason }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    @if($payment->status == 'success')
                                                        <a href="{{ route('admin.pdf.invoice', $payment->invoice_id) }}" target="_blank" class="btn btn-sm btn-success px-4">Download Kwitansi</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-clock-history display-1 text-muted opacity-25"></i>
                                        <h5 class="mt-4 text-muted">Belum ada riwayat pembayaran.</h5>
                                        <p class="text-muted">Seluruh transaksi Anda akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            {{ $payments->links() }}
        </div>
    </section>
</div>

<style>
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }
    .btn-light-primary { background-color: rgba(67, 94, 190, 0.1); color: #435ebe; }
    .btn-light-primary:hover { background-color: #435ebe; color: #fff; }
    .btn-light-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .btn-light-success:hover { background-color: #198754; color: #fff; }
</style>
@endsection
