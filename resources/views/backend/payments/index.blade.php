@extends('backend.layouts.master')

@section('title', 'Verifikasi Pembayaran')

@section('styles')
<link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/iconly.css">
@endsection

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Verifikasi Pembayaran</h3>
                <p class="text-subtitle text-muted">Konfirmasi pembayaran SPP dari siswa.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Verifikasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- Stats --}}
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Menunggu</h6>
                        <h5 class="font-extrabold mb-0 text-warning">{{ number_format($stats['pending']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Jatuh Tempo</h6>
                        <h5 class="font-extrabold mb-0 text-danger">{{ number_format($stats['overdue']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Lunas Hari Ini</h6>
                        <h5 class="font-extrabold mb-0 text-success">{{ number_format($stats['paid_today']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Pemasukan Hari Ini</h6>
                        <h5 class="font-extrabold mb-0 text-primary">{{ currency($stats['total_today']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk Action --}}
        <div id="bulkDeleteSection" class="alert alert-dark d-none animate__animated animate__fadeIn mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <span class="fw-bold"><span id="selectedCount">0</span> data dipilih</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitBulkDelete()">
                        <i class="bi bi-trash me-1"></i> Hapus Massal
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="deselectAll()">Batal</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Antrean Verifikasi</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filter --}}
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 mb-4 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1">Kelas</label>
                        <select name="class_room_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1">Bulan</label>
                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Bulan</option>
                            @foreach(\App\Models\Invoice::MONTHS as $num => $name)
                                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1">Tahun</label>
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 text-end">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light border" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>

                {{-- Main Form for Bulk Actions --}}
                <form id="bulkDeleteForm" action="{{ route('admin.payments.bulkDestroy') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small">
                                    <th width="40" class="ps-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Siswa</th>
                                    <th>Periode</th>
                                    <th>Nominal</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td class="ps-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="ids[]" value="{{ $invoice->id }}" class="form-check-input checkItem">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $invoice->student->user->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $invoice->student->classRoom->name ?? '-' }}</small>
                                    </td>
                                    <td>{{ $invoice->month_name }} {{ $invoice->year }}</td>
                                    <td class="fw-bold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($invoice->payment && $invoice->payment->method == 'gateway')
                                            <span class="badge bg-light-success text-success small px-2 py-1">
                                                <i class="bi bi-cpu me-1"></i> OTOMATIS
                                            </span>
                                        @elseif($invoice->payment)
                                            <span class="badge bg-light-info text-info small px-2 py-1">
                                                <i class="bi bi-person me-1"></i> MANUAL
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light-{{ $invoice->status_color }} text-{{ $invoice->status_color }} rounded-pill">
                                            {{ $invoice->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group shadow-sm">
                                            @if($invoice->payment)
                                                <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#proofModal{{ $invoice->id }}" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @endif
                                            
                                            @if($invoice->status != 'paid')
                                                {{-- Use JS to submit to avoid nested forms --}}
                                                <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="submitConfirm('{{ route('admin.payments.confirm', $invoice->id) }}')" title="Konfirmasi">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $invoice->id }}" title="Tolak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modals are fine here --}}
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Tidak ada tagihan yang menunggu verifikasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- Hidden Form for Confirmation --}}
                <form id="confirmForm" method="POST" style="display:none">
                    @csrf
                </form>

                {{-- Modals outside the form --}}
                @foreach($invoices as $invoice)
                    <div class="modal fade" id="proofModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title text-white">Detail Pembayaran - {{ $invoice->student->user->name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center bg-light p-4">
                                    @if($invoice->payment && $invoice->payment->method == 'gateway')
                                        <div class="py-4 mb-3 bg-white rounded shadow-sm border-dashed">
                                            <i class="bi bi-shield-lock-fill text-success display-4"></i>
                                            <h6 class="mt-3 fw-bold">Pembayaran Otomatis (Gateway)</h6>
                                            <span class="badge bg-success rounded-pill px-3 py-2">Diverifikasi Sistem</span>
                                        </div>
                                    @elseif($invoice->payment && $invoice->payment->proof)
                                        <img src="{{ Storage::url('proofs/' . $invoice->payment->proof) }}" class="img-fluid rounded shadow-sm border border-4 border-white mb-3" style="max-height: 300px; object-fit: contain;">
                                    @endif

                                    @if($invoice->payment)
                                    <div class="text-start bg-white p-3 rounded shadow-sm">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Metode</small>
                                                <span class="fw-bold text-capitalize">{{ $invoice->payment->method ?? 'Manual' }}</span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <small class="text-muted d-block">Bank/Provider</small>
                                                <span class="fw-bold">{{ $invoice->payment->bank_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Nama Pengirim</small>
                                                <span class="fw-bold small">{{ $invoice->payment->sender_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <small class="text-muted d-block">Waktu</small>
                                                <span class="fw-bold small">{{ $invoice->payment->paid_at ? $invoice->payment->paid_at->format('d/m/Y H:i') : '-' }}</span>
                                            </div>
                                            @if($invoice->payment->note)
                                            <div class="col-12 border-top pt-2 mt-2">
                                                <small class="text-muted d-block">Catatan/Log:</small>
                                                <div class="p-2 bg-light rounded small font-monospace" style="font-size: 11px;">{{ $invoice->payment->note }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-light-warning small border-0 py-2">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Data pembayaran belum tersedia untuk tagihan ini.
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer bg-light p-2">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="rejectModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('admin.payments.reject', $invoice->id) }}" method="POST" class="w-100">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Alasan Penolakan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small text-muted mb-3">Tuliskan alasan penolakan agar siswa dapat memperbaikinya.</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pesan Penolakan</label>
                                            <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Bukti transfer tidak terbaca atau nominal tidak sesuai." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-2">
                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-danger px-4">Tolak & Hubungi Siswa</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    function submitConfirm(url) {
        if (confirm('Konfirmasi pembayaran ini sebagai lunas?')) {
            const form = document.getElementById('confirmForm');
            form.action = url;
            form.submit();
        }
    }

    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.checkItem');
    const bulkSection = document.getElementById('bulkDeleteSection');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkUI() {
        const checked = document.querySelectorAll('.checkItem:checked');
        if (checked.length > 0) {
            bulkSection.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkSection.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(item => item.checked = this.checked);
            updateBulkUI();
        });
    }

    checkItems.forEach(item => {
        item.addEventListener('change', updateBulkUI);
    });

    function deselectAll() {
        if (checkAll) checkAll.checked = false;
        checkItems.forEach(item => item.checked = false);
        updateBulkUI();
    }

    function submitBulkDelete() {
        if (confirm('Hapus massal tagihan yang dipilih?')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endsection
