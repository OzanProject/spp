@extends('backend.layouts.master')

@section('title', 'Tagihan SPP')

@section('styles')
<link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/iconly.css">
@endsection

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tagihan SPP</h3>
                <p class="text-subtitle text-muted">Kelola tagihan pembayaran SPP seluruh siswa.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tagihan SPP</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Total Tagihan</h6>
                        <h5 class="font-extrabold mb-0">{{ number_format($stats['total']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Lunas</h6>
                        <h5 class="font-extrabold mb-0 text-success">{{ number_format($stats['paid']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Belum Lunas</h6>
                        <h5 class="font-extrabold mb-0 text-warning">{{ number_format($stats['unpaid']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5 text-center">
                        <h6 class="text-muted font-semibold small">Menunggu</h6>
                        <h5 class="font-extrabold mb-0 text-info">{{ number_format($stats['pending']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk Action --}}
        <div id="bulkDeleteSection" class="alert alert-dark d-none animate__animated animate__fadeIn mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-lightning-charge-fill text-warning me-2 fs-5"></i>
                    <span class="fw-bold"><span id="selectedCount">0</span> tagihan dipilih</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitBulkDelete()">
                        <i class="bi bi-trash me-1"></i> Hapus Massal
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="deselectAll()">Batal</button>
                </div>
            </div>
        </div>

        {{-- Generate Massal --}}
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Generate Tagihan Massal</h5>
            </div>
            <div class="card-body py-3">
                <form action="{{ route('admin.invoices.generateBulk') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold">Bulan</label>
                        <select name="month" class="form-select form-select-sm" required>
                            @foreach(\App\Models\Invoice::MONTHS as $num => $name)
                                <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold">Tahun</label>
                        <select name="year" class="form-select form-select-sm" required>
                            @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control form-control-sm" placeholder="350000"
                            value="{{ setting('spp_amount', 350000) }}" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="form-control form-control-sm"
                            value="{{ date('Y-m-') . str_pad(setting('due_date_day', 10), 2, '0', STR_PAD_LEFT) }}" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-warning btn-sm w-100"
                            onclick="return confirm('Generate tagihan untuk SEMUA siswa?')">
                            <i class="bi bi-lightning-charge me-1"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Tagihan</h5>
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i> Tambah Manual
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filter & Search --}}
                <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-2 mb-4 align-items-end">
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach(\App\Models\Invoice::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Kelas</label>
                        <select name="class_room_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Bulan</label>
                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Bulan</option>
                            @foreach(\App\Models\Invoice::MONTHS as $num => $name)
                                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Tahun</label>
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4 ms-auto text-end">
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-light border" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </form>

                <form id="bulkDeleteForm" action="{{ route('admin.invoices.bulkDestroy') }}" method="POST">
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
                                    <th>Siswa / Kelas</th>
                                    <th>Periode</th>
                                    <th>Nominal</th>
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
                                        <small class="text-muted">{{ $invoice->student->classRoom->name ?? '-' }} &bull; {{ $invoice->student->nis }}</small>
                                    </td>
                                    <td>{{ $invoice->month_name }} {{ $invoice->year }}</td>
                                    <td class="fw-bold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-light-{{ $invoice->status_color }} text-{{ $invoice->status_color }} rounded-pill">
                                            {{ $invoice->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('{{ route('admin.invoices.destroy', $invoice->id) }}', 'Hapus tagihan?', true)"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada data tagihan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <form id="actionForm" method="POST" style="display:none">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                </form>

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
    function confirmAction(url, message, isDelete = false) {
        if (confirm(message)) {
            const form = document.getElementById('actionForm');
            const methodField = document.getElementById('methodField');
            form.action = url;
            methodField.value = isDelete ? 'DELETE' : 'POST';
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
        if (confirm('Hapus semua tagihan yang dipilih?')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endsection
